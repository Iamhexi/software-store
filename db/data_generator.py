import random
import sys
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

    values = f'NULL, "{login}", "{password_hash}", "{username}", "{date}", "{role}"'
    return f'INSERT INTO User VALUES ({values})'

def generate_review():
    titles = [
        "Great software!",
        "Buggy but promising",
        "Excellent work",
        "Needs improvement"
    ]

    title = get_rand_from(titles)
    author_id = random.randint(1, 10)  # Assuming there are 10 users in the User table
    software_id = random.randint(1, 10)  # Assuming there are 10 software units in the SoftwareUnit table
    description = ''.join(random.choices(string.ascii_letters + string.digits, k=255))
    date_added = generate_date()
    date_last_updated = generate_date()

    values = f'NULL, {author_id}, {software_id}, "{title}", "{description}", "{date_added}", "{date_last_updated}"'
    return f'INSERT INTO Review VALUES ({values});'

def generate_rating():
    author_id = random.randint(1, 10)  # Assuming there are 10 users in the User table
    software_id = random.randint(1, 10)  # Assuming there are 10 software units in the SoftwareUnit table
    mark = random.randint(1, 5)  # Assuming a rating scale from 1 to 5
    date_added = generate_date()

    values = f'NULL, {author_id}, {software_id}, {mark}, "{date_added}"'
    return f'INSERT INTO Rating VALUES ({values})'

def generate_bug_report():
    software_id = random.randint(1, 10)  # Assuming there are 10 software units in the SoftwareUnit table
    user_id = random.randint(1, 10)  # Assuming there are 10 users in the User table
    title = f'Bug in {software_id}'
    description_of_steps_to_get_bug = ''.join(random.choices(string.ascii_letters + string.digits, k=255))
    bug_description = ''.join(random.choices(string.ascii_letters + string.digits, k=255))
    date_added = generate_date()
    review_status = get_rand_from(["pending", "resolved"])

    values = f'NULL, {software_id}, {user_id}, "{title}", "{description_of_steps_to_get_bug}", "{bug_description}", "{date_added}", "{review_status}"'
    return f'INSERT INTO BugReport VALUES ({values})'

def generate_software_unit():
    author_id = random.randint(1, 10)  # Assuming there are 10 users in the User table
    name = ''.join(random.choices(string.ascii_letters, k=10))
    description = ''.join(random.choices(string.ascii_letters + string.digits, k=255))
    link_to_graphic = "https://example.com/graphic"
    is_blocked = random.randint(0, 1)

    values = f'NULL, {author_id}, "{name}", "{description}", "{link_to_graphic}", {is_blocked}'
    return f'INSERT INTO SoftwareUnit VALUES ({values})'

def generate_category():
    category_id = random.randint(1, 10)  # Assuming there are 10 categories
    name = ''.join(random.choices(string.ascii_letters, k=10))
    description = ''.join(random.choices(string.ascii_letters + string.digits, k=255))

    return f'INSERT INTO Category VALUES ({category_id}, "{name}", "{description}")'

def generate_software_category():
    software_id = random.randint(1, 10)  # Assuming there are 10 software units in the SoftwareUnit table
    category_id = random.randint(1, 10)  # Assuming there are 10 categories

def generate_statute_violation_report():
    software_id = random.randint(1, 10)  # Assuming there are 10 software units in the SoftwareUnit table
    user_id = random.randint(1, 10)  # Assuming there are 10 users in the User table
    rule_point = random.randint(1, 10)  # Assuming there are 10 rule points
    description = ''.join(random.choices(string.ascii_letters + string.digits, k=255))
    date_added = generate_date()
    review_status = get_rand_from(["pending", "resolved"])

    values = f'NULL, {software_id}, {user_id}, {rule_point}, "{description}", "{date_added}", "{review_status}"'
    return f'INSERT INTO StatuteViolationReport VALUES ({values})'

def generate_account_change_request():
    user_id = random.randint(1, 10)  # Assuming there are 10 users in the User table
    description = ''.join(random.choices(string.ascii_letters + string.digits, k=255))
    date_submitted = generate_date()
    review_status = get_rand_from(["pending", "approved", "rejected"])

    values = f'NULL, {user_id}, "{description}", "{date_submitted}", "{review_status}"'
    return f'INSERT INTO AccountChangeRequest VALUES ({values})'

def generate_software_version():
    software_id = random.randint(1, 10)  # Assuming there are 10 software units in the SoftwareUnit table
    description = ''.join(random.choices(string.ascii_letters + string.digits, k=255))
    date_added = generate_date()
    major_version = random.randint(1, 10)
    minor_version = random.randint(1, 10)
    patch_version = random.randint(1, 100)

    values = f'NULL, {software_id}, "{description}", "{date_added}", {major_version}, {minor_version}, {patch_version}'
    return f'INSERT INTO SoftwareVersion VALUES ({values})'

def generate_source_code():
    version_id = random.randint(1, 10)  # Assuming there are 10 versions in the SoftwareVersion table
    filepath = f'/path/to/source/code/{version_id}/'

    values = f'NULL, {version_id}, "{filepath}"'
    return f'INSERT INTO SourceCode VALUES ({values})'

def generate_executable():
    version_id = random.randint(1, 10)  # Assuming there are 10 versions in the SoftwareVersion table
    target_architecture = get_rand_from(["x86", "x64", "arm"])
    date_compiled = generate_date()

    file_extension = get_rand_from([
        "exe",
        "deb",
        "msi",
        "dmg",
        "app"
    ])
    filepath = f'/path/to/executable/{version_id}/app.{file_extension}'

    values = f'NULL, {version_id}, "{target_architecture}", "{date_compiled}", "{filepath}"'
    return f'INSERT INTO Executable VALUES ({values})'

def generate_download():
    user_id = random.randint(1, 10)  # Assuming there are 10 users in the User table
    executable_id = random.randint(1, 10)  # Assuming there are 10 executables in the Executable table
    date_download = generate_date()

    values = f'NULL, {user_id}, {executable_id}, "{date_download}"'
    return f'INSERT INTO Download VALUES ({values})'

def generate(entity):
    entity = entity.lower()
    
    if entity == "user":
        return generate_user()
    elif entity == "review":
        return generate_review()
    elif entity == "rating":
        return generate_rating()
    elif entity == "bugreport":
        return generate_bug_report()
    elif entity == "statuteviolationreport":
        return generate_statute_violation_report()
    elif entity == "accountchangerequest":
        return generate_account_change_request()
    elif entity == "softwareunit":
        return generate_software_unit()
    elif entity == "softwareversion":
        return generate_software_version()
    elif entity == "sourcecode":
        return generate_source_code()
    elif entity == "executable":
        return generate_executable()
    elif entity == "download":
        return generate_download()
    elif entity == "category":
        return generate_category()
    elif entity == "softwarecategory":
        return generate_software_category()
    else:
        raise ValueError(f"Entity '{entity}' not supported for data generation.")
    

if len(sys.argv) <= 2:
    exit

entity = sys.argv[1]
numberOfEntitites = int(sys.argv[2])

for i in range(numberOfEntitites):
    print(generate(entity) + ';')