git clone https://github.com/Barrier-Theo/sortir-padel.git
git composer install


php bin/console make:migration
php bin/console doctrine:migrations:migrate
