GMV Dashboard Demo

🚀 How to Install / Run the Demo

1. Please install xampp, composer and git bash
   create a database in mysql named, geekygreens
   cd to your htdocs folder in xampp

2. Clone the repository
   Use bash terminal
   git clone https://github.com/kylesepano/geekygreens.git
   cd to the cloned folder

3. install dependencies
   composer install
   npm install

4. Set up environment
   Copy copy the .env file to the folder, make sure when uploading the env file its name should be .env you can rename it after copying
   double check the database credential in your phpmyadmin

5. Run migrations and seed data
   php artisan migrate --seed, it should create the database in mysql

6. Start the server
   php artisan serve

7. Visit the dashboard in your browser:  
   👉 http://127.0.0.1:8000/data - to add upload .json file shops.json and metrics.json
   👉 http://127.0.0.1:8000 - dashboard

---

## 🛠️ Tech Stack & Why

- Laravel 10 → Reliable backend framework, great for rapid development.
- Livewire→ Makes building interactive components (charts, filtering, tables) fast without heavy JavaScript.
- Chart.js → Lightweight charting library for visualizing GMV trends.
- Tailwind CSS → Clean, responsive UI with minimal CSS effort.

This stack was chosen to balance speed, interactivity, and maintainability while keeping the setup developer-friendly.

---

⚠️ Limitations

- Demo data: Currently uses seeded/sample data, not production-level.
- Basic filters: Date range filtering and sorting are implemented, but more advanced analytics (e.g., drill-downs) are not included.
- Styling: Layout is minimal and can be further polished for production use.
