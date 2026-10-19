# SWE40006 - Software Deployment and Evolution

This repository contains portfolio work and web application examples for the
SWE40006 Software Deployment and Evolution unit. The projects are independent:
run each application from its own project directory.

## Repository contents

| Path | Contents |
| --- | --- |
| `Portfolio1/Portfolio1App.zip` | Portfolio 1 application, supplied as a ZIP archive. |
| `Portfolio2/` | Reserved for Portfolio 2; no project files are currently included. |
| `Portfolio3/MyFirstWebApp/` | A basic ASP.NET Core Razor Pages application. |
| `Portfolio3/TaskTrackerApp/` | An ASP.NET Core Razor Pages study task tracker. |
| `Portfolio3/php-notes-board/` | A PHP notes board with a responsive, single-page interface. |

## Requirements

- .NET 10 SDK to build and run either ASP.NET Core application.
- PHP 7.4 or later to run the PHP notes board.

## Run the ASP.NET Core applications

Run these commands from the repository root in PowerShell.

### Basic web application

```powershell
dotnet run --project .\Portfolio3\MyFirstWebApp\MyFirstWebApp.csproj
```

The launch profiles use `http://localhost:5005` and
`https://localhost:7139`.

### Study task tracker

```powershell
dotnet run --project .\Portfolio3\TaskTrackerApp\TaskTrackerApp.csproj
```

The launch profiles use `http://localhost:5267` and
`https://localhost:7219`. Open the `/Tasks` page to use the tracker.

Both projects can also be built independently:

```powershell
dotnet build .\Portfolio3\MyFirstWebApp\MyFirstWebApp.csproj
dotnet build .\Portfolio3\TaskTrackerApp\TaskTrackerApp.csproj
```

The task tracker supports adding tasks, choosing a priority, filtering active
and completed tasks, marking tasks complete, deleting tasks, and clearing
completed tasks. Its sample tasks and changes are stored in application memory,
so they reset when the application restarts.

## Run the PHP notes board

From the repository root, start PHP's built-in development server:

```powershell
php -S localhost:8000 -t .\Portfolio3\php-notes-board
```

Then open `http://localhost:8000`. The board supports adding color-coded notes,
deleting individual notes, and clearing all notes. It writes its JSON data to
the PHP system temporary directory (`phpnotes.json`), so this is temporary
local storage rather than a managed or persistent database.

## Notes

- The two .NET applications are separate projects; there is no solution file at
  the repository root.
- The PHP board loads Google Fonts and Bootstrap Icons from external CDNs.
- `bin/`, `obj/`, and `publish/` are generated .NET output directories and are
  excluded by the repository's `.gitignore`.
