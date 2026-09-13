# QueueNow

QueueNow is a web-based queue management system for clinics and other service environments where visitors need to wait for their turn.

The system allows visitors to take a ticket, track their queue status, see the number of people ahead of them, and view an estimated waiting time.

Employees can manage the queue through a dedicated dashboard and control the ticket lifecycle.

## Project Overview

QueueNow focuses on solving a simple but common problem: managing waiting queues efficiently and giving visitors better visibility into their turn.

### Main Workflow

```text
Take Ticket
     ↓
Receive Ticket Number
     ↓
Track Queue
     ↓
Called
     ↓
Serving
     ↓
Done
```

## Main Users

* **Customer / Visitor** — takes a ticket and tracks their turn.
* **Employee** — manages the queue and ticket status.
* **Manager / Admin** — planned for a future version.

## Ticket Status

```text
Waiting
Called
Serving
Done
Skipped
Cancelled
```

Tickets are not deleted from the database after completion, and ticket numbers are not reused within the same day and service.

## MVP Features

* Take a Ticket
* Generate a unique Ticket Number
* View Ticket Status
* View number of people ahead
* Employee Queue Dashboard
* Next Ticket
* Start Service
* Complete Service
* Skip Ticket
* Cancel Ticket
* Persistent Ticket History
* Estimated Waiting Time
* Frontend + Backend + Database integration

## AI Feature

QueueNow includes an **Estimated Waiting Time** feature using a Regression Model.

The AI team will determine the appropriate features based on the available queue data and evaluate the model before integrating it with the main system.

The prediction is an estimate and should not be considered a guaranteed waiting time.

## Team Structure

* **Frontend**
* **Backend + Database**
* **UI/UX**
* **AI**
* **QA + System Analysis**
* **Project Management**

## Development

The MVP is being developed within **one week** by a team of trainees.

The main goal is to deliver a complete working system rather than separate independent components.

```text
Frontend
    ↓
Backend API
    ↓
Database
    ↓
AI Prediction
```

## Documentation

Project documentation is maintained in the `docs` directory.

* [Software Requirements Specification (SRS)](./docs/SRS.md)

Additional system design and technical documentation will be added as the project progresses.

## GitHub Workflow

All project work is managed through GitHub.

Each team member is expected to:

* Work on clearly defined tasks.
* Create clear and meaningful commits.
* Contribute through the project repository.
* Collaborate through branches and pull requests when required.
* Keep work integrated with the complete system.

## Project Status

🚧 **In Development — MVP**

## Team

**QueueNow Project Team**
