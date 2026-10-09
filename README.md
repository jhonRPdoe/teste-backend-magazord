# Teste Técnico - Desenvolvedor Back-end Magazord

Sistema para gerenciamento e consulta de pessoas e seus respectivos contatos, desenvolvido como parte do processo seletivo para a vaga de Desenvolvedor Back-end na **Magazord.com.br**.

## 🚀 Tecnologias e Recursos Utilizados

- **Back-end:** PHP 8.2 (Sem a utilização de frameworks)
- **Persistência / ORM:** Doctrine ORM
- **Front-end:** HTML5, CSS3 e JavaScript (Vanila JS com chamadas assíncronas via API/Fetch)
- **Banco de Dados:** MySQL
- **Gerenciador de Dependências:** Composer
- **Ambiente de Desenvolvimento:** Docker e Docker Compose
- **Testes Automatizados:** PHPUnit

## 🛠️ Como Executar o Projeto com Docker

Certifique-se de ter o **Docker** instalado na sua máquina.

1. **Clone o repositório** para o seu ambiente local.
2. Na raiz do projeto, execute o comando para compilar e subir os containers em segundo plano:
   ```bash
   docker compose up -d --build
   ```
3. Acesse a linha de comando do container do PHP para instalar as dependências do Composer:
   ```bash
   docker compose exec web bash
   ```
4. Dentro do container, instale as dependências executando:
   ```bash
   composer install
   ```
5. Para gerar a estrutura das tabelas automaticamente no banco de dados através do mapeamento das entidades do Doctrine, execute:
   ```bash
   php bin/doctrine.php orm:schema-tool:create
   ```
6. O sistema estará pronto e disponível para acesso através do seu navegador no endereço:
   **`http://localhost:8080`**

## 🧪 Como Executar os Testes Unitários

Para rodar a esteira de testes automatizados, certifique-se de estar dentro do container da aplicação e execute:

```bash
vendor/bin/phpunit tests
```

---
**Desenvolvido por:** Ruan Pereira — ruanpdev@outlook.com  
*Outubro / 2026*