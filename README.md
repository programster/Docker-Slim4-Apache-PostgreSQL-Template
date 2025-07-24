Docker Slim4 Apache PostgreSQL Template
================================

A template for quickly deploying a PHP 8.4 Slim4 web application/API using the Apache webserver, 
through Docker.


## Setting Up

### Config Files
Create a `.env` file from the `.env.example` template and fill in accordingly.


## Deploying

To deploy the web application just run:

```bash
docker compose build
docker compose up
```

If you wish to develop the web application, then you probably want to bind-mount the site, which
is already set up for you if you run the following instead:

```bash
docker compose -f docker-dev-compose.yml up
```



