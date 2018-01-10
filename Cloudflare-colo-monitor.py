from urllib.parse import urlencode
import json
import logging
import urllib.request as urlrequest
import re
import os

log = logging.getLogger()


class Slack():
    def __init__(self, webhook_url=""):
        self.webhook_url = webhook_url
        self.opener = urlrequest.build_opener(urlrequest.HTTPHandler())

    def notify(self, **kwargs):
        return self.send(kwargs)

    def send(self, payload):
        payload_json = json.dumps(payload)
        data = urlencode({"payload": payload_json})
        req = urlrequest.Request(self.webhook_url)
        response = self.opener.open(req, data.encode('utf-8')).read()
        return response.decode('utf-8')


def fetch_colo(domain):
    rel_path = '/cdn-cgi/trace'
    with urlrequest.urlopen('https://' + domain + rel_path) as response:
        data = response.read().decode('utf-8')

    m = re.search("^colo=([A-Z]{3})", data, re.MULTILINE)
    return m.group(1)


def lambda_handler(event, context):
    colo = fetch_colo(os.environ.get('TARGET_DOMAIN'))
    print(colo)

    expected_colo = os.environ.get('EXPECTED_COLO')

    if colo != expected_colo:
        Slack(webhook_url=os.environ.get('SLACK_WEBHOOK_URL')) \
            .notify(text="Some traffic from CloudFlare is being routed via %s. (expected: %s)" % (colo, expected_colo),
                    icon_emoji=":cloudflare:")

    assert context
    log.debug(event)
