# Teleimg

> Storage images with [Telegra.ph](https://telegra.ph)

![image.png](https://telegra.ph/file/68daae403d0ff4ab8bb88.png)

# Deploy

## Docker compose
```
docker-compose build
docker-compose up -d
```

## Manually
- Prepare *nginx(apache)* + *php* environment
- Clone this repository and move this to your Web directory
- (nginx) Include `./teleimg.conf` or paste the whole content to your nginx configuration file.
- Done! 🎉 Just visit `http://localhost:8088` on your own machine