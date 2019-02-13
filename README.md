# Kinect Consulting website

## Local Development

### Project dependencies
* [git](https://git-scm.com/)
* [docker](https://www.docker.com/)
* [ahoy (ver 2)](https://github.com/ahoy-cli/ahoy)

### Project setup 
```
# Clone repository
git clone git@github.com:weknowinc/drupal.develop.git

# Copy .env file
cp .env.dist .env

# Add hostname entry in your /etc/hosts file
127.0.0.1    drupal.develop

# Start containers
ahoy up
```

### Sync Project
```
# Switch develop branch
git checkout master

# Fetch latest changes
git fetch upstream

# Merge changes locally
git merge upstream/master

# Sync forked repo 
git push origin/master

# Download new dependencies
ahoy composer install

# Build Drupal site
ahoy drupal build:develop

# Create branch
git checkout -b BRANCH_NAME 

# Add file changes
git add -p

# Commit changes
git commit -m MESSAGE_SUBJECT

# Push code to repository
git push origin BRANCH_NAME
```

### Using Composer 
```
ahoy composer install

ahoy composer require drupal/MODULE_NAME

ahoy composer remove drupal/MODULE_NAME
```

### Export Configuration
```
# Using default drupal config system  
ahoy drupal config:export --no-interaction

# Using config_split
ahoy drupal config_split:export --no-interaction
```
## Production

## How to build, resolving dependencies through composer

In order to get all dependencies for Drupal site, you need to run composer in an environment with the same PHP version of Official Drupal Docker image.

###### Requirements for local env to retrieve dependencies
1. PHP CLI version 7.2.14 (to release it on docker image for latest Drupal version [v8.6.7](https://hub.docker.com/_/drupal/))
2. Composer package manager. [get-composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)

###### Requirement for release
1. Docker
2. MySQL or MariaDB database running

### Execute Composer
In order to build the application, we use composer to get Drupal core, modules and his dependencies. 

Execute composer install in the root application directory (where composer.json/lock files are located)

```
$ cd /PATH/TO/kinect-consulting.com
$ composer install

```

Once composer finished you should have all the files required by Drupal site.

### Release Stage
In order to have the _builded_ site up&running, you need to build the `Dokerfile` provided.

`Dockerfile` is based on Official Docker image, it copies the required files into the container.
Alternatively, you can avoid using the provided Dockerfile and use Drupal Docker image directly, more details in the _volumes_ [official documentation](https://hub.docker.com/_/drupal/#volumes)

#### Environment Variables
Provided `Dockerfile` uses environment variables to set database `host`, `name`, `username` and `password`
You can set this variables creating a file `docker_env`, and passing it with `--file-env` option

example:

```
#Example of docker_env file content
DATABASE_HOST=host-database
DATABASE_PORT=3306
DATABASE_NAME=kinectconsulting_db
DATABASE_USER=drupal
DATABASE_PASSWORD=drupal
```

Also, you can pass environment variables using docker run command (See below: Running Dockerfile)

To disable environment variables you can set a build-time argument (See below: Build Dockerfile)

#### Build Dockerfile (release stage)
To build release stage, using environment variables, you should execute

```
$ docker build . -t drupal-kc
```

To disable environment variables, you can execute:

```
$ docker build --build-arg DISABLE_ENV_VARS=1 --target drupal . -t drupal-kc
```

#### Running Dockerfile (release stage)
###### Using Environment Variables
If you are using environment variables, you must provide a `docker_env` file, or set the variables using `-e` flag of `docker run` command

With `docker_env` file:

```
$ docker run --env-file=docker_env \
    --name drupal-kinect-consulting \
    -p 80:80 \
    -d drupal-kc
```

Using `-e` on `docker run` command:

```
$ docker run \
    -e DATABASE_HOST=host-database \
    -e DATABASE_PORT=3306 \
    -e DATABASE_NAME=kinectconsulting_db \
    -e DATABASE_USER=drupal \
    -e DATABASE_PASSWORD=drupal \
    --name drupal-kinect-consulting \
    -p 80:80 \
    -d drupal-kc
```

###### Whitout Environment Variables
If you built the image using the ARG `DISABLE_ENV_VARS=1`, then just run the container
```
$ docker run \
    --name drupal-kinect-consulting \
    -p 80:80 \
    -d drupal-kc
```

### Installing Profile Kinect Consulting site
If everything went ok, you will be able to access it via http://localhost or http://host-or-ip in a browser, and you will see the Drupal installation wizard, then select the `Kinect Consulting` profile
and follow the installation process.

If you are using a release stage with environment variables, the process will start automatically after select the `Kinect Consulting` profile.

If you disabled the environment variables, a form will be displayed to set the database connection data.


## How to build using multi-stage build

To achive the final result of this project the most similar to the official drupal image, we take advantage of docker [multistage build](https://docs.docker.com/develop/develop-images/multistage-build/), and the command to build this image is:

```
$ docker build -f Dockerfile_multistage --target drupal . -t drupal-kc
```

### How to use this image

A MySQL database instance running is needed.

You can use an Official docker image, for database type MySQL or MariaDB
Database name/username/password: see environment variables in the description for [mysql](https://hub.docker.com/_/mysql/)

```
$ docker run --name some-mysql \
    -e MYSQL_ROOT_PASSWORD=my-secret-pw \
    -e MYSQL_ROOT_HOST='%' \
    -e MYSQL_DATABASE=drupal \
    -e MYSQL_USER=drupal \
    -e MYSQL_PASSWORD=drupal \
    -d mysql \
    --default-authentication-plugin=mysql_native_password

```

The basic pattern for starting the Drupal instance is:

```
$ docker run --name drupal-kinect-consulting -p 80:80 -d drupal-kc
```

The instance uses environment variables to connect to database. The variables are
```
DATABASE_HOST
DATABASE_PORT
DATABASE_NAME
DATABASE_USER
DATABASE_PASSWORD
```

#####Option 1)

You can set the environment variables creating a `docker_env` file and using the `--env-file` option of `docker run` command:

```
#Example of docker_env file content
DATABASE_HOST=some-mysql
DATABASE_PORT=3306
DATABASE_NAME=drupal
DATABASE_USER=drupal
DATABASE_PASSWORD=drupal
```
and then execute `docker run`
```
$ docker run --env-file=docker_env --name drupal-kinect-consulting -p 80:80 -d drupal-kc
```

#####Option 2)
alternatively, you can set environment variables in the `docker run` command:

Example to run Drupal instance using the previous database name/username/password of mysql instance example

```
$ docker run \
    -e DATABASE_HOST=some-mysql \
    -e DATABASE_PORT=3306 \
    -e DATABASE_NAME=drupal \
    -e DATABASE_USER=drupal \
    -e DATABASE_PASSWORD=drupal \
    --link some-mysql:mysql \
    --name drupal-kinect-consulting \
    -p 80:80 \
    -d drupal-kc
```

###Profile Installation

When Drupal instance be running, you will be able to access it via http://localhost or http://host-or-ip in a browser.

If everything went ok, you will see the Drupal installation wizard, then select the `Kinect Consulting` profile
and follow the installation process.

### Persist sites/default/files

You can set a docker volume in order to persist the content of `sites/default/files` in a host path:
```
$ docker run --env-file=docker_env \
    --name drupal-kinect-consulting \ 
    -p 80:80 \
    -d \
    -v /path/on/host/files:/var/www/html/site/default/files \
    drupal-kc
```

### Patched files

Copy patched files from `patched` directoy to the correct path in the docker instance, for example:

```
COPY --from=builder /app/patched/MonthDate.php /var/www/html/core/modules/views/src/Plugin/views/argument/MonthDate.php
```

## Updating Drupal version

At the moment of writing this README, the latest version of Drupal is `8.6.7` so that is the default configured version.

In order to use another version, you must update `composer.json` and `composer.lock` (`composer update` command).
 
If you are using the `Dockerfile` or `Dockerfile_multistage` provided, you should set the argument `DRUPAL_VERSION`, or use build-time variables in the `docker build` command:

```
$ docker build --build-arg DRUPAL_VERSION=8.6.7 --target drupal . -t drupal-kc
```

After update Drupal version, you should go to `http://host-or-ip/update.php` to complete the processes. 


Note: remember update patched files in `patched` directory and the corresponding COPY command in Dockerfile.

Enjoy. 
