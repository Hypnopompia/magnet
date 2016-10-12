HOST=http://magnet.local
TOKEN=`cat token`

curl -s \
-X POST \
-H "Content-Type: application/json" \
-H "Authorization: Bearer $TOKEN" \
--data-binary "@skill.json" \
$HOST/api/alexa-skill
