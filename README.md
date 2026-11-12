# Centre Formation

A web application for managing the activities of a training centre. The project is being developed as a team learning assignment, with a focus on practising Agile collaboration, and Git/GitHub workflows.

## Project scope

The application is planned around three user spaces:

- **Admin:** manage students, trainers, groups, modules, and assignments.
- **Formateur:** access assigned groups and modules, manage sessions, record attendance, and manage evaluations and grades.
- **Étudiant:** view a personal dashboard, profile information, attendance, and results.

The exact functionality may evolve as the team works through the project backlog. This README describes the intended direction; it should be updated as features are implemented.

## Technologies

- HTML5, CSS3, and JavaScript
- PHP (native PHP, POO; no PHP framework)
- MySQL for database management
- Apache as the web server (via XAMPP)
- Git and GitHub for version control and collaboration
- Jira for task and progress tracking

## Requirements

Install the following on your computer:

- XAMPP with Apache, MySQL, and PHP 8.x
- Git
- A web browser
- A code editor (e.g., Visual Studio Code, PhpStorm, neovim)

## Installation

### 1. Get the project

Clone the repository into your XAMPP `htdocs` directory:

```bash
git clone https://github.com/ababdelo/centre-formation.git
code centre-formation
```

For a typical XAMPP installation, the project directory should be similar to:

- Windows: `C:\\xampp\\htdocs\\centre-formation`
- Linux: `/opt/lampp/htdocs/centre-formation`

### 2. Configure your local database connection

Copy the example environment file:

```bash
cp env/.env.example env/.env
```

On Windows, you can also copy the file manually using File Explorer.

Open `env/.env` and set the values for your own XAMPP installation. For example:

```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=centre_formation
DB_USER=root
DB_PASS=""
```

Your MySQL port, username, and password may be different. **Keep `env/.env` local: it is ignored by Git and must never be committed.** Only `env/.env.example` is shared with the team.

### 3. Create and populate the database

1. Start Apache and MySQL from the XAMPP Control Panel.
2. Open phpMyAdmin at `http://localhost/phpmyadmin` or `http://localhost:8001`.
3. Copy the contents of `database/schema.sql` and run it to create the database and all needed and available tables.
4. Import or copy the contents of `database/seed.sql` to add the project's sample data, if needed.

**Import the schema before the sample data. If the team changes the SQL filenames, update these instructions accordingly.**

### 4. Open the application

Visit:

```text
http://localhost/centre-formation/
```

## Project structure

The project is organised by user space and shared code:

```text
centre-formation/
├── admin/                 # Administration pages
├── auth/                  # Login and authentication pages
├── formateur/             # Trainer pages
├── etudiant/              # Student pages
├── assets/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── images/            # Images and icons
├── classes/               # PHP classes representing project entities
├── config/
│   └── connection.php     # Database connection
├── database/
│   ├── schema.sql         # Database structure
│   └── seed.sql           # Sample data
├── env/
│   ├── .env.example       # Shared configuration template
│   └── .env               # Local configuration (not committed)
├── includes/              # Shared PHP files and helpers
├── index.php              # Application entry point
├── .gitignore             # Files and directories ignored by Git
├── CONTRIBUTING.md        # Guidelines for contributing to the project
├── LICENSE                # Project license (MIT)
└── README.md              # Project documentation file
```

Some directories or files may not exist yet; they will be added as the related features are developed.

## Collaboration

Read [CONTRIBUTING.md](CONTRIBUTING.md) before starting work. The team uses Jira to track tasks and GitHub branches and pull requests to review and integrate changes.

## License

This project is distributed under the MIT License. See [LICENSE](LICENSE) for details.