max=$1
maxy=$2
max=`echo 'SELECT value_ FROM \`fixcompu_new\`.config WHERE name="MAX_THREADS";' | mysql -u fixcompu_new -pdamnpassword123!@# | tail -n 1`
x=`ps -Af | grep wget | grep -c "scraper.php"`;
echo $x;
if [ $x -lt $max ] ; then
	r=$RANDOM;
	echo $r;
	i=1;
	y=$(( $max - $x ));
	while [ $i -le $y ] ; do
		wget -O /dev/null -o /dev/null "http://winhook.org/script_new/admin/scraper.php?cron=1"  2>&1 &
		i=$(( $i + 1 ));
	done;
fi;