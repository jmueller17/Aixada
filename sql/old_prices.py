from MySQLdb import connect

def get_db():
    return connect(user="aixada", passwd="aixada", db="aixada")

def shop_dates(db):
    return ['2005-01-12']
#    c = db.cursor()
#    c.execute("""select """)
#    res = c.fetchall()

def ufs_at_date(db, date):
    c = db.cursor()
    c.execute("""
select 
  distinct uf_id
from
  aixada_order_item
where 
  date_for_order = %s
order by uf_id""",
              (date,))
    return c.fetchall()
    
def total_spent(db, uf_id, date):
    c = db.cursor()
    c.execute("""
select 
  -sum(quantity) 
from 
  aixada_account 
where 
  account_id=%s 
  and 
  ts between %s and %s + interval 1 day
  and
  quantity < 0;
""", (1000 + uf_id, date, date))
    return c.fetchall()[0][0]

def products_bought(db, uf_id, date):
    c = db.cursor()
    c.execute("""
select 
  quantity, product_id
from 
  aixada_order_item
where 
  date_for_order = %s and uf_id = %s
group by product_id""",
              (date, uf_id,))
    return c.fetchall()

def make_eqs(uf, products, spent, eqs):
    if spent is not None:
        eqstr = 'uf' + str(uf) + ': ' + ' + '.join([str(p[0]) + ' x' + str(p[1]) for p in products]) + ' + ptol - ntol = ' + str(spent)
        eqs.append(eqstr)

def max_lp(eqs, vars, special_var):
    return 'minimize 1000 ptol + 1000 ntol + x' + str(special_var) + '\nsubject to\n' + \
        '\n'.join(eqs) + \
        '\nbounds\nx' + \
        ' >= 0\nx'.join([str(v) for v in varset]) + \
        '\n'.join([' >= 0', 'ptol >= 0', 'ntol >= 0'])

if __name__ == "__main__":
    db = get_db()
    eqs = []
    vars = []
    date = '2005-01-26'
    for _uf in ufs_at_date(db, date):
        uf = _uf[0]
        pb = products_bought(db, uf, date)
        print uf, pb
        for p in pb:
            vars.append(p[1])
        ts = total_spent(db, uf, date)
        make_eqs(uf, pb, ts, eqs)
    varset = set(vars)
    for v in varset:
        f = open('prob.lp', 'w')
        f.write(max_lp(eqs, vars, v))
        break
