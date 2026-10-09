# Teste Técnico - Desenvolvedor Back-end Magazord

Sistema para gerenciamento e consulta de pessoas e seus respectivos contatos, desenvolvido como parte do processo seletivo para a vaga de Desenvolvedor Back-end na **Magazord.com.br**.

## Tecnologias e Recursos Utilizados

- **Back-end:** PHP 8.2 (Sem a utilização de frameworks)
- **Persistência / ORM:** Doctrine ORM
- **Front-end:** HTML5, CSS3 e JavaScript (Vanila JS com chamadas assíncronas via API/Fetch)
- **Banco de Dados:** MySQL
- **Gerenciador de Dependências:** Composer
- **Ambiente de Desenvolvimento:** Docker
- **Testes Automatizados:** PHPUnit

## Como Executar o Projeto

Com o **Docker** instalado na sua máquina:

1. **Clone o repositório** para o seu ambiente local.
2. Na raiz do projeto, execute o seguinte comando para compilar e subir os containers:
   ```bash
   docker compose up -d --build
   ```
3. Acesse a linha de comando do container:
   ```bash
   docker compose exec web bash
   ```
4. Instale as dependências do Composer:
   ```bash
   composer install
   ```
5. Inicie a geração automática das tabelas no banco de dados:
   ```bash
   php bin/doctrine.php orm:schema-tool:create
   ```
6. O sistema estará pronto para acesso através do seu navegador no link:
   **`http://localhost:8080/pessoas`**

## Como Executar os Testes Unitários

Para rodar os testes automatizados, acesse o container da aplicação e execute:

```bash
vendor/bin/phpunit tests
```

---
**Desenvolvido por:** Ruan Pereira — ruanpdev@outlook.com  
*Outubro / 2026*
