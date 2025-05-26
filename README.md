# Car Service Registry

Aplicatie web pentru gestionarea intrarilor in service ale autovehiculelor personale.

## Functionalitati principale

✅ **Gestiune intrari service**  
- Adauga, editeaza, vizualizeaza si sterge inregistrari  
- Detalii complete: data, kilometraj, numar inmatriculare, service, actiuni efectuate, piese inlocuite, cost  

✅ **Sistem avansat de filtrare**  
- Cauta dupa cuvinte cheie in actiuni service sau piese  
- Filtreaza dupa: interval de date, service, numar inmatriculare  

## Tehnologii utilizate

- Laravel 12
- SQLite

## Instalare

1. Cloneaza repository-ul
2. Instaleaza dependinte: `composer install`
3. Configureaza baza de date si Google OAuth credentials in `.env`
4. Instaleaza dependinte:
```bash
composer install
composer require laravel/socialite
composer require socialiteproviders/google

```
4. Ruleaza migrarile: `php artisan migrate`
5. Porneste serverul: `php artisan serve`

## Utilizare

1. Acceseaza aplicatia in browser
2. Autentifica-te cu Google.
3. Adauga intrari noi prin butonul "Adaugă intrare"
4. Foloseste filtrele pentru a gasi inregistrari specifice
5. Editeaza sau sterge intrari dupa necesitate

## Contributii

Contributiile sunt binevenite! Pentru modificari majore, deschide un issue pentru a discuta schimbarile propuse.


## Licenta

[MIT](https://choosealicense.com/licenses/mit/)