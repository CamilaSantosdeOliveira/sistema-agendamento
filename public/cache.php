<?php
/**
 * EduConnect - Sistema de Cache
 * Versão: 3.0
 * 
 * Sistema de cache simples para melhorar performance
 */

class SimpleCache {
    private $cache_dir;
    private $default_ttl = 3600; // 1 hora
    
    public function __construct($cache_dir = null) {
        $this->cache_dir = $cache_dir ?: __DIR__ . '/cache';
        
        // Criar diretório se não existir
        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }
    
    /**
     * Obter valor do cache
     * @param string $key Chave do cache
     * @return mixed|null Valor em cache ou null
     */
    public function get($key) {
        $file = $this->getCacheFile($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($file));
        
        // Verificar se expirou
        if (time() > $data['expires']) {
            $this->delete($key);
            return null;
        }
        
        return $data['value'];
    }
    
    /**
     * Armazenar valor no cache
     * @param string $key Chave do cache
     * @param mixed $value Valor a armazenar
     * @param int $ttl Tempo de vida em segundos
     * @return bool Sucesso
     */
    public function set($key, $value, $ttl = null) {
        $ttl = $ttl ?: $this->default_ttl;
        $file = $this->getCacheFile($key);
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        return file_put_contents($file, serialize($data), LOCK_EX) !== false;
    }
    
    /**
     * Deletar do cache
     * @param string $key Chave do cache
     * @return bool Sucesso
     */
    public function delete($key) {
        $file = $this->getCacheFile($key);
        
        if (file_exists($file)) {
            return unlink($file);
        }
        
        return true;
    }
    
    /**
     * Limpar todo o cache
     * @return bool Sucesso
     */
    public function clear() {
        $files = glob($this->cache_dir . '/*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    /**
     * Verificar se existe no cache
     * @param string $key Chave do cache
     * @return bool Existe e não expirou
     */
    public function has($key) {
        return $this->get($key) !== null;
    }
    
    /**
     * Obter arquivo de cache
     * @param string $key Chave do cache
     * @return string Caminho do arquivo
     */
    private function getCacheFile($key) {
        $safe_key = md5($key);
        return $this->cache_dir . '/' . $safe_key . '.cache';
    }
    
    /**
     * Obter ou calcular (cache-aside pattern)
     * @param string $key Chave do cache
     * @param callable $callback Função para calcular valor se não estiver em cache
     * @param int $ttl Tempo de vida
     * @return mixed Valor
     */
    public function remember($key, $callback, $ttl = null) {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = call_user_func($callback);
        $this->set($key, $value, $ttl);
        
        return $value;
    }
}

// Instância global
$cache = new SimpleCache();

/**
 * Função helper para cache
 */
function cache($key = null, $value = null, $ttl = 3600) {
    global $cache;
    
    if ($key === null) {
        return $cache;
    }
    
    if ($value === null) {
        return $cache->get($key);
    }
    
    return $cache->set($key, $value, $ttl);
}
?>


