// EduConnect Service Worker
// Versão: 1.0.1

const CACHE_NAME = 'educonnect-v1.0.1';
const urlsToCache = [
  './',
  './dashboard_corrigido.php',
  './cursos_completo.php',
  './manifest.json'
];

// Instalação do Service Worker
self.addEventListener('install', event => {
  console.log('🔄 Service Worker instalando...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('✅ Cache aberto:', CACHE_NAME);
        
        // Adicionar arquivos um por um com tratamento de erro
        const cachePromises = urlsToCache.map(url => {
          return cache.add(url)
            .then(() => {
              console.log('✅ Cacheado:', url);
            })
            .catch(error => {
              console.log('⚠️ Erro ao cachear:', url, error.message);
              // Não falhar se um arquivo não puder ser cacheado
              return Promise.resolve();
            });
        });
        
        return Promise.all(cachePromises);
      })
      .then(() => {
        console.log('🎉 Service Worker instalado com sucesso!');
        // Forçar ativação imediata
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('❌ Erro na instalação do Service Worker:', error);
      })
  );
});

// Interceptação de requisições
self.addEventListener('fetch', event => {
  // Ignorar requisições não-GET
  if (event.request.method !== 'GET') {
    return;
  }
  
  // Ignorar requisições para APIs externas
  if (event.request.url.includes('chrome-extension') || 
      event.request.url.includes('extension') ||
      event.request.url.includes('analytics')) {
    return;
  }
  
  event.respondWith(
    caches.match(event.request)
      .then(cachedResponse => {
        if (cachedResponse) {
          console.log('📦 Retornando do cache:', event.request.url);
          return cachedResponse;
        }
        
        // Se não está no cache, buscar na rede
        return fetch(event.request)
          .then(response => {
            // Não cachear respostas de erro
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }
            
            // Clonar a resposta para cache
            const responseToCache = response.clone();
            
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
                console.log('💾 Adicionado ao cache:', event.request.url);
              });
            
            return response;
          })
          .catch(error => {
            console.log('🌐 Erro na rede, tentando cache:', event.request.url, error);
            
            // Retornar página offline se disponível
            if (event.request.destination === 'document') {
              return caches.match('./');
            }
            
            return new Response('Erro de conexão', {
              status: 503,
              statusText: 'Service Unavailable'
            });
          });
      })
  );
});

// Ativação do Service Worker
self.addEventListener('activate', event => {
  console.log('🚀 Service Worker ativando...');
  
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('🗑️ Removendo cache antigo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
    .then(() => {
      console.log('✅ Service Worker ativado!');
      // Tomar controle de todas as páginas abertas
      return self.clients.claim();
    })
  );
});

// Mensagens do Service Worker
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
