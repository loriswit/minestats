# Minestats
Display and compare players statistics from one or multiple Minecraft servers.

## Installation

### Client

Clone the repository and navigate to the *client* directory.
```sh
cd client
```

Define the VUE_APP_API_URL environment variable.
```bash
export VUE_APP_API_URL=http://host:port
```

Install dependencies and build the client into the *dist* directory.
```sh
npm install
npm run build
```

### Server
**Warning**: The web server must be running on a host that has access to the Minecraft server files.

Clone the repository and navigate to the *server* directory.
```sh
cd server
```

Copy *config.json.example* to *config.json* and edit it.
Specify the properties of the **running** server in the *server* field.
Add optional non-running servers in the *archived* field.
```sh
cp config.json.example config.json
vim config.json
```

Install dependencies.
```sh
composer install
```

Start a web server that forwards all requests to *public/index.php*.
