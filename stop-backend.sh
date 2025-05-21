echo "Oprire servicii backend"

brew services stop nginx
brew services stop php 
brew services stop redis
brew services stop postgresql@14 

echo "Toate serviciile au fost oprite!"
