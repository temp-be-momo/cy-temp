# !! @servers must be on a single line !!
@servers(['servers' => ['cyrange@cyrange02.cylab.be', 'cyrange@cyrange.cylab.be']])

@setup
    $root = '/home/cyrange';
    $tmp = '/tmp/cyrange';
@endsetup


@task('deploy', ['on' => 'servers'])
    echo 'fetch new docker-compose.yml ...'
    rm -Rf {{ $tmp }}
    mkdir {{ $tmp }}
    cd {{ $tmp }}
    wget https://download.cylab.be/cyrange-web/latest/cyrange.zip
    unzip cyrange.zip
    mv -f docker-compose.yml {{ $root }}

    echo 'restart updated containers ...'
    cd {{ $root }}
    docker-compose up -d
@endtask

