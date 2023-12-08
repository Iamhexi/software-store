import random
import string

def get_rand_from(array):
    id = random.randint(0, len(array)-1)
    return array[id]


def generate_date():
    
    day = random.randint(1, 28)
    if day < 10:
        day = '0' + str(day)

    month = random.randint(1, 12)
    if month < 10:
        month = '0' + str(month)
        
    year = random.randint(2023, 2024)

    return f'{year}-{month}-{day}'


def generate_user():
    usernames = [
        "John",
        "Emma",
        "Emily",
        "Robert"
    ]

    username = get_rand_from(usernames)

    login = username.lower() + str(random.randint(1000, 9999))
    
    roles = [
        "client",
        "client",
        "client",
        "client",
        "client",
        "author",
        "author",
        "admin"
    ]

    role = get_rand_from(roles)
    password_hash = ''.join(random.choices(string.ascii_lowercase, k=128))
    date = generate_date()

    return f'NULL, "{login}", "{password_hash}", "{username}", "{date}", "{role}"'

for i in range(10):
    user = generate_user()
    print(f'INSERT INTO User VALUES ({user});')
    print()