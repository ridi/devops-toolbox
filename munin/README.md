# munin scripts
munin 관리를 위해 만든 스크립트들입니다


### asana uptime notifier 
uptime이 warning 한도를 넘으면 warning 한도를 +20일 늘린 후 asana에 이슈를 생성합니다.

- 설치 방법
    - 소스를 받은 후 make를 실행한다
    - make는 소스, munin 디렉토리, munin.conf의 소유자를 munin으로 변경하고 composer 패키지를 설치한다
    ```
      git clone https://github.com/ridibooks/devops-toolbox
      cd devops-toolbox/munin && make
    ```

    - Asana 프로젝트 id와 Asana api 토큰을 .env에 입력한다.
    ```
      cp .env.sample .env
      vim .env
    ```
    
    - munin.conf에 asana contact를 추가한다 (/path/to/munin은 devops-toolbox/munin까지의 절대경로)
    ```
    contact.asana.command | /path/to/devops-toolbox/munin/uptime_warning.sh "${loop<,>:wfields ${var:label}}" ${var:group} ${var:host} ${loop<,>:wfields ${var:value}} ${loop<,>:wfields ${var:wrange}}
    contact.asana.always_send warning
    ```

    - 적용할 node contact설정에 asana를 추가한다.
    ```
    [myteam;mygroup;mynode]
    ...
    contacts mail asana
    ...
    ```
### debugging
`munin` 계정으로 로그인하여 스크립트를 실행해본다.
```
su - munin --shell=/bin/bash
run_uptime_warning.sh uptime 'performance;proxy' proxy155 100 10
```
