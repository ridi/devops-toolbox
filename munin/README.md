# munin scripts
munin 관리를 위해 만든 스크립트들입니다


### asana uptime notifier 
uptime이 warning 한도를 넘으면 warning 한도를 +20일 늘린 후 asana에 이슈를 생성합니다.

- 설치 방법
    - munin서버에서 소스를 받는다.
    ```
      git clone https://github.com/ridibooks/devops-toolbox
    ```
    
    - 소스, munin 디렉토리, munin.conf의 소유자를 munin으로 변경한다.
    ```
      chown -R munin:munin devops-toolbox/munin
      chown munin:munin /etc/munin /etc/munin/munin.conf
    ```
    
    - 실제 사용할 [asana 프로젝트 id]와 [asana api 토큰]을 넣어 실행한다. 
    ```
      cd devops-toolbox/munin/asana_client
      sed "s/<RIDI-PROJECT-ID>/[asana 프로젝트 id]/" create-uptime-task.php.template | sed "s/<ASANA-ACCESS-TOKEN>/[asana api 토큰]/" > create-uptime-task.php
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
