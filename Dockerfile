# Utilize uma imagem base do PHP com extensões necessárias
FROM php:7.4-fpm

# Instale as extensões necessárias e o composer
RUN apt-get update && apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Instale o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Defina o diretório de trabalho
WORKDIR /var/www

# Remova a pasta html padrão e copie a aplicação Laravel
RUN rm -rf /var/www/html
COPY . /var/www

# Instale as dependências do projeto
RUN composer install

# Copie o arquivo .env.example para .env e gere a chave da aplicação
RUN cp .env.example .env
RUN php artisan key:generate

# Dê permissões de escrita para as pastas storage e bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage
RUN chown -R www-data:www-data /var/www/bootstrap/cache

# Exponha a porta que a API utilizará (substitua 8000 pela porta correta, se necessário)
EXPOSE 8000

# Inicie o servidor embutido do PHP
CMD php artisan serve --host=0.0.0.0 --port=8000
