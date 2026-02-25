<p align="center">
  <img src="public/images/privacy-tool.png" width="200" alt="Privacy Tool Logo">
</p>

# Privacy Tool

O **Privacy Tool** é uma plataforma robusta desenvolvida para auxiliar organizações na gestão de privacidade, conformidade e proteção de dados. A ferramenta centraliza o gerenciamento de inspeções, projetos e colaboração em equipe em um único ambiente intuitivo.

## 🚀 Funcionalidades Principais

- **Gestão de Projetos**: Organize suas iniciativas de conformidade por projetos dedicados.
- **Inspeções de Privacidade**: Realize avaliações detalhadas baseadas em questionários estruturados e categorias específicas.
- **Colaboração em Equipe**: Convide membros para seus projetos e gerencie permissões de acesso.
- **Resultados e Snapshots**: Visualize o progresso da conformidade através de snapshots históricos e relatórios de resultados.
- **Questionários Flexíveis**: Suporte a múltiplas versões de questionários e seções organizadas.

## 💻 Stack Tecnológica

- **Backend**: [Laravel 12](https://laravel.com)
- **Frontend**: [Vue.js 3](https://vuejs.org) com [Inertia.js](https://inertiajs.com)
- **Estilização**: [Tailwind CSS](https://tailwindcss.com)
- **Banco de Dados**: MySQL / PostgreSQL / SQLite

## 🛠️ Instalação e Configuração

### Pré-requisitos

- PHP 8.2 ou superior
- Node.js & NPM
- Composer

### Passo a Passo

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/wellingtondellamura/privacy-tool2.git
   cd privacy_tool2
   ```

2. **Instale as dependências do PHP:**
   ```bash
   composer install
   ```

3. **Instale as dependências do Frontend:**
   ```bash
   npm install
   ```

4. **Configure o ambiente:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Execute as migrações:**
   ```bash
   php artisan migrate
   ```

6. **Inicie o servidor de desenvolvimento:**
   ```bash
   npm run dev
   ```

## 📄 Licença

Este projeto é um software de código aberto licenciado sob a [MIT license](https://opensource.org/licenses/MIT).
