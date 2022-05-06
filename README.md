Requeriments:
- Docker (https://www.docker.com/)
- Docker Compose (https://docs.docker.com/compose/install/)
- Postman (https://www.postman.com/)

Documentation:
- Postman (https://documenter.getpostman.com/view/6339482/UyxdJoqS)

Run:
Create network:

```docker-Network
docker network create --subnet=10.253.252.0/24 gestaoDeCadastrosMedicosNetworks
```
up Docker Compose:
```Docker
docker-compose up --build -d
```

Prepar ambinte database:
```sql
docker exec php_8_gestao_de_cadastros_medicos bash -c " php artisan migrate"
```

Popular database Especialidades:
```sql
docker exec php_8_gestao_de_cadastros_medicos bash -c " php artisan db:seed --class=especialidadeSeeder"
```