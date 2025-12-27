FROM nginx:latest

# Skopiuj plik konfiguracyjny Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Stwórz katalog snippets i skopiuj fastcgi-php.conf
RUN mkdir -p /etc/nginx/snippets
COPY snippets/fastcgi-php.conf /etc/nginx/snippets/fastcgi-php.conf

# Ustawiamy katalog roboczy
WORKDIR /app

# Kopiujemy package.json oraz package-lock.json
COPY package*.json ./

# Kopiowanie własnej konfiguracji Nginx, jeśli jest potrzeba
COPY nginx.conf /etc/nginx/nginx.conf

# Otwieranie portu 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
