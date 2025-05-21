echo "Pornesc serviciile backend..."

brew services start postgresql@14 
brew services start redis
brew services start php
brew services start nginx

echo "Toate serviciile au fost pornite cu success!"
