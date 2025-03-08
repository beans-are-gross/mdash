<?php
shell_exec("sudo dpkg-divert --divert /usr/bin/caddy.default --rename /usr/bin/caddy");
shell_exec("sudo mv ./caddy /usr/bin/caddy.custom");
shell_exec("sudo update-alternatives --install /usr/bin/caddy caddy /usr/bin/caddy.default 10");
shell_exec("sudo update-alternatives --install /usr/bin/caddy caddy /usr/bin/caddy.custom 50");
shell_exec("sudo systemctl restart caddy");