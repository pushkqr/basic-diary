# Diary App

A simple diary application built with PHP and MySQL.

## Features

- Create new diary entries with a title, date, content, and an optional image.
- Store and retrieve entries from a MySQL database.

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/pushkqr/diary-php.git
   cd diary-php
   ```

2. Set up your database:

   ```sql
   CREATE DATABASE diary_app;
   USE diary_app;
   CREATE TABLE entries (
       id INT AUTO_INCREMENT PRIMARY KEY,
       title VARCHAR(255) NOT NULL,
       date DATE NOT NULL,
       content TEXT NOT NULL,
       image VARCHAR(255) DEFAULT NULL
   );
   ```

3. Configure your database connection in `inc/db.inc.php` with your environment variables.

4. Start your local server and navigate to the project directory.

## Usage

- Access the main page to view diary entries.
- Use the form to add new entries.
