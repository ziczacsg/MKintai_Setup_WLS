#!/usr/bin/env bash
# Dọn dẹp WSL trước khi export. Chạy bằng user ubuntu.
set -u

echo "==> [1/8] Dọn Docker (image/volume/cache build)"
if command -v docker >/dev/null 2>&1; then
  sudo systemctl start docker 2>/dev/null || true
  sudo docker system prune -af --volumes 2>/dev/null || true
fi

echo "==> [2/8] Dừng dịch vụ để dữ liệu được flush sạch"
sudo systemctl stop apache2 mysql redis-server ssh ssh.socket \
                    docker docker.socket containerd 2>/dev/null || true

echo "==> [3/8] Dọn cache gói"
sudo apt-get clean
sudo apt-get autoremove --purge -y
sudo rm -rf /var/lib/apt/lists/*

echo "==> [4/8] Dọn cache ngôn ngữ (npm, composer, ...)"
export NVM_DIR="$HOME/.nvm"; [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"
command -v npm >/dev/null && npm cache clean --force
command -v composer >/dev/null && composer clear-cache
rm -rf ~/.cache/* ~/.npm/_logs

echo "==> [5/8] Xoá log & file tạm"
sudo journalctl --rotate 2>/dev/null; sudo journalctl --vacuum-time=1s 2>/dev/null
sudo rm -rf /var/log/journal/*
sudo find /var/log -type f \( -name '*.gz' -o -name '*.[0-9]' -o -name '*.old' \) -delete
sudo find /var/log -type f -exec truncate -s 0 {} \;
sudo rm -rf /tmp/* /var/tmp/*

echo "==> [6/8] Xoá CA & chứng chỉ HTTPS (mỗi máy sẽ tự sinh CA riêng khi khởi động lần đầu)"
sudo rm -f /etc/ssl/local-dev/*.pem
rm -rf ~/.local/share/mkcert
sudo rm -f /usr/local/share/ca-certificates/flow-dev-local-ca.crt
sudo update-ca-certificates --fresh >/dev/null 2>&1

echo "==> [7/8] Xoá thông tin cá nhân / bí mật / định danh riêng của máy"
rm -rf ~/.claude ~/.claude.json
rm -f  ~/.ssh/id_* ~/.ssh/authorized_keys ~/.ssh/known_hosts
sudo rm -f /etc/ssh/ssh_host_*
git config --global --unset user.name  2>/dev/null
git config --global --unset user.email 2>/dev/null
rm -f ~/.git-credentials
rm -f ~/.bash_history ~/.mysql_history ~/.lesshst ~/.viminfo ~/.node_repl_history ~/.rediscli_history
rm -rf ~/.vscode-server
sudo rm -f /var/lib/mysql/auto.cnf

echo "==> [8/8] Ghi dấu phiên bản image"
echo "flow-dev image 1.0.0 - $(date -I)" | sudo tee /etc/flow-dev-release >/dev/null

echo
echo "Dung lượng các thư mục lớn nhất:"
sudo du -xh --max-depth=1 / 2>/dev/null | sort -h | tail -8
echo
echo "XONG. Chạy:  unset HISTFILE; exit   rồi export từ PowerShell."
