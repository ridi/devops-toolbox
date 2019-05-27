const AWS = require('aws-sdk');
const mysql = require('mysql2/promise');
const ssm = new AWS.SSM({ apiVersion: '2014-11-06' });


async function loadSSMParam(name) {
    var ssmParams = {
        Name: name,
        WithDecryption: true
    };

    return new Promise((resolve, reject) => {
        ssm.getParameter(ssmParams, function (err, data) {
            if (err) console.log(err);
            else resolve(data.Parameter.Value);
        });
    });
}

function putMetricData(namespace, metricName, value, unit) {
    let cloudWatch = new AWS.CloudWatch();
    let params = {
        MetricData: [{
            MetricName: metricName,
            Value: value,
            Timestamp: new Date(),
            Unit: unit,
        }],
        Namespace: namespace
    };
    console.log(`LOG|${value}|${unit}|${metricName}|${namespace}`);

    cloudWatch.putMetricData(params, function (err, data) {
        if (err) console.log(err, err.stack); // an error occurred
        else console.log(data); // successful response
    });
}

exports.handler = async (event, context, callback) => {
    try {
        let namespace = event.cloudwatch_namespace;
        let metricName = event.cloudwatch_metric_name;

        let password = await loadSSMParam(event.mariadb_password);
        let port = event.mariadb_port;
        if (!port) {
            port = 3306;
        }

        let dbConn = await mysql.createConnection({
            host: event.mariadb_host,
            user: event.mariadb_user,
            port: port,
            password: password,
        });
        let [rows, fields] = await dbConn.execute('SHOW SLAVE STATUS');
        let status = JSON.parse(JSON.stringify(rows))[0];
        putMetricData(namespace, metricName, status.Seconds_Behind_Master, 'Seconds');

        await dbConn.end();

        let response = {
            statusCode: 200,
            body: '',
        };
    
        callback(null, response);
    } catch (err) {
        console.log(err);

        let response = {
            statusCode: 500,
            body: err,
        };
    
        callback(null, response);
    }
};