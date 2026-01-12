# Diretório de Uploads

Este diretório armazena arquivos enviados pelos usuários e livros gerados.

## Estrutura

```
uploads/
├── temp/       # Fotos temporárias enviadas pelos usuários
└── books/      # PDFs dos livros gerados (futuramente)
```

## Segurança

- **temp/**: Arquivos com mais de 24 horas são deletados automaticamente pelo script `cleanup-temp-files.php`
- **books/**: PDFs gerados são mantidos permanentemente (gerenciados via admin)
- Todos os arquivos são ignorados pelo Git (ver `.gitignore`)

## Permissões

- Diretórios: `755` (drwxr-xr-x)
- Arquivos: `644` (-rw-r--r--)

## Limpeza Automática

Execute o script de limpeza via cronjob:

```bash
# Executar diariamente às 3h da manhã
0 3 * * * /usr/bin/php /caminho/para/cleanup-temp-files.php
```

Ou via navegador (com chave secreta):

```
https://seudominio.com/cleanup-temp-files.php?key=sua-chave-secreta
```

## Tipos de Arquivo Permitidos

- JPEG (.jpg, .jpeg)
- PNG (.png)
- WebP (.webp)

## Limites

- Tamanho máximo: **5MB**
- Dimensões mínimas: **200x200 pixels**
