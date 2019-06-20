OLDDIR=`pwd`
echo "Exporting current.sql"
mysqldump -h localhost -u root -proot testmerge2 -r /tmp/mergeoutput.sql
echo "Export Done, Chunk it"
mkdir /tmp/mergesplit
cd /tmp/mergesplit
/root/splitter/sqlsplitter /tmp/mergeoutput.sql 50000000
for file in *.sql; do dest="${file//[[:space:]]/.}" && mv -i "$file" "${dest//[^[:alnum:]._-]/}"; done
rename 's/mergeoutput/db/' *.sql
cd $OLDDIR
TIMESTAMP=`date +%s`
mkdir ./.db/current/$TIMESTAMP
cp /tmp/mergesplit/*.sql ./.db/current/$TIMESTAMP/
chmod 777 ./.db/current/$TIMESTAMP/*
rm -Rf /tmp/mergesplit
rm ./.db/current/*.sql



