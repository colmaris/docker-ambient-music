# Deployment of the Ambient Music player with Docker Stack

This project uses Docker to deploy a Ambient music player with Nginx as the web server.

## Prerequisites

- Docker installed on your machine
- Docker Compose installed

## Installation

1. Clone the repository

`git clone https://github.com/colmaris/ambient.git`

2. Navigate to the project directory:

   `cd src/`


3. Copy your music file in mp3

4. Build and start the containers with Docker Compose:

   docker-compose up --build

## Accessing the Application

Once the containers are running, open your browser and go to `http://localhost`. You should see the page generated by `index.php`.

### Generate playlist.

Enter in the php container `docker compose exec php bash` and navigate to the public directory. Then execute the `playlit_gen.php` script : `php playlist_gen.php`. All done and enjoy your music !

## Stopping the Containers

To stop the containers, you can use:

`docker compose down`