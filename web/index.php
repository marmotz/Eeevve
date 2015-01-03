<?php
    include __DIR__ . '/../vendor/autoload.php';

    $app = new Marmotz\Eeevve\WebApp(__DIR__ . '/../wallirc.yml');
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <link rel="stylesheet" href="/vendor/html5-boilerplate/css/normalize.css">
    <link rel="stylesheet" href="/vendor/html5-boilerplate/css/main.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Raleway|Inconsolata" type="text/css">
    <link rel="stylesheet" href="/vendor/gl-datepicker/styles/glDatePicker.default.css">
    <link rel="stylesheet" href="/css/main.css">
    <script src="/vendor/html5-boilerplate/js/vendor/modernizr-2.6.2.min.js"></script>
</head>
<body>
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <header>
        Channel:
        <select id="channelSelector">
            <option value="<?php echo $app->generateUrl($channel); ?>"<?php if ($app->getChannel() === null): ?> selected<?php endif; ?>>
            </option>
            <?php foreach ($app->getChannels() as $channel): ?>
                <option value="<?php echo $app->generateUrl($channel); ?>"<?php if ($app->getChannel() === $channel): ?> selected<?php endif; ?>>
                    <?php echo $channel; ?>
                </option>
            <?php endforeach; ?>
        </select>

        Date:
        <input
            id="dateSelector"
            value="<?php echo $app->getDateString(); ?>"
            data-baseurl="<?php echo $app->generateUrl($channel, ''); ?>"
            data-min="<?php echo $app->getMinTimestamp(); ?>"
            data-max="<?php echo $app->getMaxTimestamp(); ?>"
        />
    </header>

    <section>
        <?php $logs = $app->getLogs(); ?>
        <?php if ($logs): ?>
        <table>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="timestamp">
                        [<?php echo date('H:i:s', $log['timestamp']); ?>]
                    </td>
                    <td class="nick">
                        <?php
                            switch($log['type']) {
                                case 'join':
                                case 'nick':
                                case 'part':
                                case 'quit':
                                    echo '*';
                                break;

                                case 'message':
                                    if ($log['data']['isAction']) {
                                        echo '*';
                                    } else {
                                        echo $log['data']['from']['nick'];
                                    }
                                break;
                            }
                        ?>
                    </td>
                    <td class="message">
                        <?php
                            switch($log['type']) {
                                case 'join':
                                    printf(
                                        '%s (%s@%s) vient de rentrer',
                                        $log['data']['from']['nick'],
                                        $log['data']['from']['user'],
                                        $log['data']['from']['host']
                                    );
                                break;

                                case 'message':
                                    if ($log['data']['isAction']) {
                                        echo $log['data']['from']['nick'] . ' ';
                                    }

                                    echo $log['data']['message'];
                                break;

                                case 'nick':
                                    printf(
                                        '%s est maintenant connu en tant que %s',
                                        $log['data']['from']['nick'],
                                        $log['data']['nick']
                                    );
                                break;

                                case 'part':
                                    printf(
                                        '%s vient de quitter le channel',
                                        $log['data']['from']['nick']
                                    );
                                break;

                                case 'quit':
                                    printf(
                                        '%s vient de quitter IRC (%s)',
                                        $log['data']['from']['nick'],
                                        $log['data']['message']
                                    );
                                break;
                            }
                        ?>
                    </td>
                    <!--td>
                        <?php dump($log); ?>
                    </td-->
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>Aucun log</p>
        <?php endif; ?>
    </section>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="/vendor/html5-boilerplate/js/plugins.js"></script>
    <script src="/vendor/html5-boilerplate/js/main.js"></script>
    <script src="/vendor/gl-datepicker/glDatePicker.js"></script>
    <script src="/js/main.js"></script>
</body>
</html>
