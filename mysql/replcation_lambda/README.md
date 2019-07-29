## 리플리케이션 모니터링

Replication 정보를 확인해서 CloudWatch에 전송하는 lambda 코드입니다.
동일한 코드를 여러번 사용할 수 있도록 CloudWatch Event를 사용합니다

# 설정 방법

-`index.js`, `package.json` 다운로드 하고 npm install 후 코드를 `node_modules` 를 포함하여 zip 으로 압축합니다. 
- Lambda 에서 함수생성 -> 새로 작성을 선택 후 기본 정보 및 권한 을 설정 하고 함수 생성을 눌러 함수를 생성합니다.
- Lambda 에서 필요한 권한
    - VPC 접근 권한 (`AWSLambdaVPCAccessExecutionRole`)
    - KMS Decrypt 권한 (`kms:Decrypt`)
    - SSM paramter 접근 권한 (`ssm:GetParameters`)
    - CloudWatch Metric write 권한 (`cloudwatch:PutMetricData`)

- 새롭게 함수가 생성 되었으면 함수 코드 메뉴에서 코드 입력 유형을 `.zip 파일 업로드` 로 선택, 위에서 만들어둔 압축 파일을 업로드 합니다.
- VPC, 서브넷, 보안그룹을 설정 하고 오른쪽 상단의 저장을 눌러줍니다.

- CloudWatch 서비스로 들어가서 이벤트-규칙 메뉴로 들어가 규칙 생성을 눌러줍니다.
- 이벤트 소스는 일정 - 고정비율로 선택 후 원하는 주기 (ex. 1분) 를 선택 후 대상 추가를 눌러 위에서 생성한 람다 함수를 선택합니다.
- 입력 구성을 눌러 상수(JSON 텍스트) 를 눌러 필요한 정보를 넣어줍니다.
    ```
    {
        "cloudwatch_namespace": "RDS",
        "cloudwatch_metric_name": "name", //CloudWatch 에서 지표로 확인할 이름
        "mariadb_host": "host",
        "mariadb_user": "user",
        "mariadb_port": 3306,
        "mariadb_password": "parameter_store_name" // 비밀번호는 SSM Parameter store에 등록해주세요
    }
    ```
- 세부 정보 구성을 눌러 이름과 설명을 넣고 규칙 생성을 합니다.
- CloudWatch 지표를 통해 리플리케이션 모니터링을 시작합니다.

# 전송되는 Metric 정보
- Seconds Behind Master
- Slave IO Running
- Slave SQL Running

# 추가 정보
- CloudWatch 경보를 사용하여 위에서 기록한 지표를 선택 하여 알림을 만들 수 있습니다.
- Lambda 함수 콘솔에서 인라인 편집기를 사용하여 디버깅을 할 수 있습니다.
- Lambda 함수에서 SSM 연결시 Timeout 이 난다면 SSM 의 엔드포인트를 설정 해 보는 방법이 있습니다. [관련정보](https://stackoverflow.com/questions/52134100/parameter-store-request-timing-out-inside-of-aws-lambda)  