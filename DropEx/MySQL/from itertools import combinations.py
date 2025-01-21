from itertools import combinations

# Branch details
branches = [
    'Dhaka, Bangladesh',
    'Paris, France',
    'Berlin, Germany',
    'Madrid, Spain',
    'Rome, Italy',
    'New York, USA',
    'London, UK',
    'Sydney, Australia',
    'Tokyo, Japan',
    'Beijing, China',
    'Moscow, Russia',
    'Cairo, Egypt',
    'Cape Town, South Africa',
    'Rio de Janeiro, Brazil'
]

# Function to generate SQL

def generate_combinations_sql(branches):
    combinations_list = list(combinations(branches, 2))
    
    sql_statements = []
    for i, (state1, state2) in enumerate(combinations_list, start=1):
        # Example cost calculation (random or based on specific logic)
        cost = 1000 + (i % 10) * 100
        sql = f"INSERT INTO pricing ( State_1, State_2, Cost) VALUES ( '{state1}', '{state2}', {cost});"
        sql_statements.append(sql)
    return sql_statements

# Generate and print all combinations in SQL format
sql_statements = generate_combinations_sql(branches)

for statement in sql_statements:
    print(statement)
