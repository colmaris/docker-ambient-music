# Start with PHP 8.3 FPM (FastCGI Process Manager)
FROM php:8.3-fpm

# Update package list and install dependencies
RUN apt-get update && apt-get install -y \
    git \                    
    curl \                   
    libpng-dev \            
    libonig-dev \           
    libxml2-dev \           
    libicu-dev                  

# Clean up to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
# Each one serves specific purpose:
RUN docker-php-ext-install \           
    mbstring \               
    bcmath \                              
    opcache               

# Redis for caching/sessions
RUN pecl install redis && docker-php-ext-enable redis


# Add our PHP config
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www

# What command to run
CMD ["php-fpm"]

# Document that we use port 9000
EXPOSE 9000