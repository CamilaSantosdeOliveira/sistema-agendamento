import glob
import re

files = glob.glob('*_professor.php') + glob.glob('*_professor_*.php')
files = [f for f in files if f != 'dashboard_professor.php' and f != 'cursos_professor.php']

for f in files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    modified = False
    
    # Adicionar CSS
    if 'dark-mode.css' not in content:
        content = re.sub(r'(</style>\s*</head>)', r'    <link rel="stylesheet" href="dark-mode.css">\n\1', content)
        modified = True
        
    # Adicionar Botão
    if 'dark-mode-toggle' not in content:
        content = re.sub(r'(<body[^>]*>)', r'\1\n    <!-- Dark Mode Toggle -->\n    <button class="dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode">\n        <i class="fas fa-moon"></i>\n    </button>', content, count=1)
        modified = True
        
    # Adicionar Script
    if 'dark-mode.js' not in content:
        content = re.sub(r'(</body>)', r'    <script src="dark-mode.js"></script>\n\1', content)
        modified = True
        
    if modified:
        with open(f, 'w', encoding='utf-8') as file:
            file.write(content)
        print(f"Modificado: {f}")
    else:
        print(f"Sem alterações: {f}")
