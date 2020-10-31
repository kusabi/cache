FROM php:7.4-cli

# Install redis server
RUN apt-get update && apt-get install -y \
	redis-server

# Install extra PHP libraries
RUN pecl install redis-5.1.1 \
	&& pecl install xdebug-2.8.1 \
	&& docker-php-ext-enable redis xdebug

# Copy the source code
COPY . /usr/src/cache

# Set the working directory
WORKDIR /usr/src/cache

# Expose redis port
EXPOSE 6379

# Set the entrypoint script
ENTRYPOINT ["/usr/src/cache/docker-entrypoint.sh"]