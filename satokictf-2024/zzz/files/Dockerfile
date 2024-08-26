FROM ubuntu:22.04

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get -y install openssh-server

# thanks to https://github.com/SECCON/SECCON2022_online_CTF/blob/46742099d094a69c214f35498718b5c9ba900b26/misc/txtchecker/build/Dockerfile#L10
WORKDIR /app

RUN groupadd -r ctf && useradd -m -r -g ctf ctf
RUN echo "ctf:ctf" | chpasswd

RUN echo 'ForceCommand "/app/zzz.sh"' >> /etc/ssh/sshd_config
RUN echo 'Port 5000' >> /etc/ssh/sshd_config
RUN mkdir /var/run/sshd

COPY flag.txt /
COPY zzz.sh /app/

RUN chmod 444 /flag.txt
RUN chmod 555 /app/zzz.sh

CMD /sbin/sshd -D