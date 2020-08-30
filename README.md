# Arrow Robot — non-human teammate of engineering team.

## Usage
* Adapt backend module according to your needs, provide `config.php`.

* Install dependencies.
```bash
docker run --rm  -v $(pwd):/app -u $(id -u ${USER}):$(id -g ${USER}) composer composer install --ignore-platform-reqs
```

* Run it.
```bash
docker run -d --rm -u $(id -u ${USER}):$(id -g ${USER})  -p 8888:8888 -v $(pwd):/app -w /app chialab/php:7.4 php -S 0.0.0.0:8888
```

* Build the robot physically, following [the manual](https://medium.com/@ivastly/internal-recruitment-done-right-weve-built-a-colleague-for-dev-team-a77c6d18f7b6).

* Create [sketch/Credentials.h](/sketch/Credentials.h) and put your Wi-Fi credentials and backend API host there.

* Upload the [sketch](/sketch) to robot.

* Vuala! The robot will notify the team about the hotline immediately.

![paper-signals-arrow](/arrow-video.gif)
