# Contributing

Thanks for contributing to Centre Formation. This is a team learning project, so keep changes understandable, communicate with the team, and avoid adding unnecessary complexity.

## Before starting

1. Check Jira to find your assigned task.
2. Make sure you understand the expected result and which part of the application it affects.
3. Tell the relevant teammates before making a change that affects shared files, database structure, or another person's work.
4. Update your local `dev` branch before creating a task branch.

## Branch workflow

The shared branches are:

- `main`: the stable version of the project.
- `dev`: the branch where completed and reviewed work is integrated.

Create a temporary branch for each task from `dev`. Use a short, descriptive name (e.g..):

```bash
git switch dev
git pull origin dev
git switch -c feature/admin-student-list
```

Suggested prefixes:

- `feature/` : a new feature
- `fix/` : a bug fix
- `docs/` : documentation changes
- `refactor/` : code restructuring without changing intended behaviour

Examples: `feature/formateur-attendance`, `fix/login-validation`, `docs/update-readme`.

Do not commit directly to `main` or `dev`. Do not create permanent branches for each team; create a pull request and the technical experts and ci tests and scripts will review and merge the changes into `dev`.

## Commits

Make focused commits that describe the change. Use a short message with a type prefix:

```text
feat: add student list page
fix: validate login credentials
style: improve dashboard spacing
docs: update installation steps
refactor: reuse database connection helper
chore: add PHP lint workflow
```

Avoid vague messages such as `update`, `changes`, or `final version`. A commit should ideally contain one logical change.
Avoid staging all files at once; stage only the files that are relevant to the change.

## Pull requests

1. Push your task branch to GitHub.
2. Open a pull request targeting `dev`.
3. Link the relevant Jira task in the pull request description.
4. Explain what changed and how you tested it. Add screenshots for visible UI changes if relevant.
5. The technical experts will review the pull request, suggest changes, if approved, they will merge it into `dev` and close the pull request. If not, they will request changes and you can update the branch and push again.

Do not merge your own pull request without the review agreed. Keep pull requests focused; split unrelated work into separate requests.

## Code guidelines

- Follow the existing project structure and naming conventions.
- Keep the implementation simple and readable. Do not introduce a framework, library, or new architectural pattern without discussing it with the team first.
- Use PDO prepared statements for database queries;
- Keep database connection settings in `env/.env`, they will be loaded automatically by `config/connection.php`. Do not hardcode credentials in PHP source files.
- Never commit passwords, tokens, private keys, personal data, or the local `env/.env` file.
- Keep HTML, CSS, JavaScript, and PHP organised in their intended locations where practical.
- When changing the database, update `database/schema.sql` and the sample data file when relevant. Tell the team about changes that require everyone to recreate or update their local database.

## Before opening a pull request

Check the following:

- [ ] The change matches the Jira task.
- [ ] The page or feature works locally in XAMPP.
- [ ] No PHP errors or obvious browser-console errors remain.
- [ ] Database queries use prepared statements.
- [ ] No local configuration, credentials, or unrelated files are included.
- [ ] The branch is up to date with `dev` and the pull request targets `dev`.

## Questions and coordination

For unclear requirements or shared-file conflicts, discuss the change with the relevant teammate before implementing a separate solution. Prefer agreeing on one approach over having multiple people modify the same part independently.