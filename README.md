# Project Management System

A web-based project management system designed for teams to collaborate on tasks and projects. This system allows users to create projects, assign tasks, and manage workflows efficiently.

---

## Features

- **User Roles**:
  - **Chef**: Can create projects, assign tasks, and manage team members.
  - **Member**: Can view assigned tasks and update their status.

- **Project Management**:
  - Create projects with a name, description, and status (public or private).
  - Assign tasks to team members.
  - Track task progress (Backlog, To Do, In Progress, Completed).

- **Join Requests**:
  - Members can send join requests to Chefs.
  - Chefs can approve or reject join requests.

- **Kanban Board**:
  - Visualize tasks in a Kanban-style board.
  - Drag-and-drop functionality for updating task status (optional).

- **Inbox**:
  - View pending join requests.
  - Notifications for new requests.

---

## Technologies Used

- **Frontend**:
  - HTML, CSS, JavaScript
  - Tailwind CSS for styling

- **Backend**:
  - PHP for server-side logic
  - MySQL for database management

- **Database**:
  - Tables: `users`, `projects`, `tasks`, `join_requests`

---

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (e.g., Apache, Nginx)

### Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Youcode-Classe-E-2024-2025/Hamza-Atig-project.git
   ```

2. **Set up the database**:
   - Import the SQL file located in the `database/` directory into your MySQL server.
   - Update the database connection details in `config/database.php`.

3. **Configure the web server**:
   - Point your web server to the `public/` directory.
   - Ensure `.htaccess` (for Apache) or equivalent configuration is set up for URL rewriting.

4. **Run the application**:
   - Open your browser and navigate to the project URL (e.g., `http://localhost/project-management-system`).

---

## Usage

### For Chefs

1. **Login**:
   - Use your Chef credentials to log in.

2. **Create a Project**:
   - Navigate to the "Create Project" page.
   - Fill in the project details (name, description, status).
   - Assign tasks to team members.

3. **Manage Join Requests**:
   - View pending join requests in the inbox.
   - Approve or reject requests.

### For Members

1. **Login**:
   - Use your member credentials to log in.

2. **View Tasks**:
   - Navigate to the Kanban board to view assigned tasks.
   - Update task status as you progress.

3. **Send Join Requests**:
   - Send a join request to a Chef to join their team.

---

## Database Schema

### Tables

1. **users**:
   - `user_id` (Primary Key)
   - `username`
   - `password`
   - `role` (Chef or Member)

2. **projects**:
   - `project_id` (Primary Key)
   - `project_name`
   - `description`
   - `created_by` (Foreign Key to `users.user_id`)
   - `assigned_to` (Foreign Key to `users.user_id`)
   - `status` (public or private)

3. **tasks**:
   - `task_id` (Primary Key)
   - `task_title`
   - `project_id` (Foreign Key to `projects.project_id`)
   - `status` (Backlog, To Do, In Progress, Completed)

4. **join_requests**:
   - `request_id` (Primary Key)
   - `user_id` (Foreign Key to `users.user_id`)
   - `receiver_id` (Foreign Key to `users.user_id`)
   - `status` (Pending, Approved, Rejected)

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeatureName`).
3. Commit your changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature/YourFeatureName`).
5. Open a pull request.

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
