# TeamSpeak Query  bot - Channel Deleter [![GitHub license](https://img.shields.io/github/license/Ondra3211/ts-channel-creator)](https://github.com/Ondra3211/ts-channel-creator/blob/master/LICENSE)

| GIF #1 | GIF #2 |
| ------------- | ------------- |
| ![](https://i.ondranies.tech/ja/cJbhJfeXRh.gif)  | ![](https://i.ondranies.tech/ja/tlUjWP7viK.gif)  |

## What is this?

TeamSpeak query bot that creates automatically channel by joing to specific channel

## Installation
**Requirements**
* Install the TS3 PHP Framework by [manually downloading](https://github.com/ronindesign/ts3phpframework/archive/master.zip) it or using Composer:
```
composer require planetteamspeak/ts3-php-framework
```

## Configuration
<details>
    <summary>config.ini</summary>
  
```ini
[BOT]
host = "127.0.0.1"
qport = 10011
vport = 9987
username = "serveradmin"
password = "r8GPMB+Q"
nickname = "Channel Creator"
default_channel = 31

[SETTINGS]
main_channel = 1797
create_channel = 1804
move_channel = 1803
channel_admin = 25

[MESSAGES]
channel_name = "[NICKNAME]'s channel"
kick_create = "You already own channel!"
kick_move = "You don't own any chnannel!"
create_channel = "Channel created. Password: [B][PASSWORD][/B]. You can change password to you own."
```
</details>

## Usage
For example using screen:
```
screen -AmdS 'channel creator' php bot.php
```

## License
```
MIT License

Copyright (c) 2019 Ondřej Niesner

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
