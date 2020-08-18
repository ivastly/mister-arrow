# Arrow Robot — the first ever non-human teammate of engineering team.

## Usage
* Adapt backend module according to your needs, provide `config.php` file and run it somewhere.

```bash
docker run --rm -p 8888:8888 -v $(pwd):/app -w /app chialab/php:7.4 php -S 0.0.0.0:8888
```

* build the robot physically, following [the paper signals manual](https://papersignals.withgoogle.com/).

* put your Wi-Fi credentials to [sketch/Credentials.h](/sketch/Credentials.h)

* upload the [sketch](/sketch)

* Vuala! The robot will notify the team about the hotline immediately.
