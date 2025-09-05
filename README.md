# Booking System

This is a Laravel-based web application.

## Prerequisites

- PHP >= 8.x  
- Composer  
- Node.js & npm  
- MySQL or any supported database  

## Installation

1. **composer install**  
2. **npm install**  
3. **php artisan storage:link**
4. **php artisan queue:work** 

This used for sending emails, notifications (I use mailtrap and it's installed in .env so no worries about sending real emails, Also I use my own pusher app installed in .env also) when creating bookings from seeders

4. **php artisan migrate**  

5. **php artisan schedule:work**

This to auto clean bookings

6. **php artisan db:seed**  

7. **php artisan serve**


## Auth Step

Just import postman collection
You will see login request inside Auth Folder
I added script to take care of the rest (Setting Token inside collection token to be used in other endpoints).
note: Default admin credentials is there (Coming from Seeder)
