in composer.json eintragen:
        "cunningsoft/logging-bundle": "0.1.*",

in AppKernel.php eintragen:


in services anlegen:
    pdo:
        class: PDO
        arguments: ['mysql:dbname=onlinetennis_classic;host=server3.cunningsoft.de', %database_classic_user%, %database_classic_password%]


add logging handler to monolog config:
pdo:
    type: service
    id: cunningsoft.monolog.handler.pdo
    level: debug
