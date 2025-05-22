# SB
Website massage
Even nagaan of dit werkt.

DATABASE:

Kolom
Type
Lengte
Verplicht?
Extra info
id
INT
–
✅
PRIMARY KEY, AUTO_INCREMENT
name
VARCHAR
255
✅
email
VARCHAR
255
✅
phone
VARCHAR
50
❌
optioneel
address
VARCHAR
255
✅
massage_choice
VARCHAR
255
✅
preferred_time
VARCHAR
255
❌
optioneel
comments
TEXT
–
❌
optioneel
submission_time
DATETIME
–
✅
default: CURRENT_TIMESTAMP
recaptcha_success
BOOLEAN
–
✅
recaptcha_score
FLOAT
–
❌
optioneel (v3 of score logging)
