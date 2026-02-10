<?php

namespace App\Libraries;

class CareerTransitionAI
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = getenv('OPENAI_API_KEY');
    }

    public function analyzeTransition($currentRole, $targetRole)
    {
        $prompt = "Analyze career transition from {$currentRole} to {$targetRole}. Provide ONLY valid JSON:
{
  \"skill_gaps\": [\"skill1\", \"skill2\", \"skill3\"],
  \"timeline\": \"X weeks\",
  \"roadmap\": [{\"phase\": \"Phase 1\", \"duration\": \"2 weeks\", \"focus\": \"description\"}],
  \"daily_tasks\": [{\"day\": 1, \"title\": \"task title\", \"description\": \"task description\", \"duration\": 10}]
}";

        $response = $this->callOpenAI($prompt);
        $data = json_decode($response, true);
        
        if (!$data || !isset($data['skill_gaps'])) {
            return $this->getFallbackData($currentRole, $targetRole);
        }
        
        return $data;
    }

    public function generateCourseContent($currentRole, $targetRole, $skillGaps)
    {
        $skills = implode(', ', $skillGaps);
        $prompt = "Create detailed offline course for {$currentRole} to {$targetRole}. Skills: {$skills}. Provide JSON:
{
  \"modules\": [
    {
      \"number\": 1,
      \"title\": \"Module Title\",
      \"description\": \"Overview\",
      \"weeks\": 2,
      \"lessons\": [
        {
          \"number\": 1,
          \"title\": \"Lesson Title\",
          \"content\": \"Detailed lesson content with examples\",
          \"resources\": [\"Resource 1\", \"Resource 2\"],
          \"exercises\": [\"Exercise 1\", \"Exercise 2\"]
        }
      ]
    }
  ]
}";

        $response = $this->callOpenAI($prompt);
        $data = json_decode($response, true);
        
        if (!$data || !isset($data['modules'])) {
            return $this->getFallbackCourse($currentRole, $targetRole);
        }
        
        return $data;
    }

    private function getFallbackData($currentRole, $targetRole)
    {
        $lowerCurrent = strtolower($currentRole);
        $lowerTarget = strtolower($targetRole);
        
        // PHP to Next.js/React transition
        if (strpos($lowerCurrent, 'php') !== false && (strpos($lowerTarget, 'next') !== false || strpos($lowerTarget, 'react') !== false)) {
            return [
                'skill_gaps' => ['JavaScript ES6+', 'React Components', 'JSX', 'Hooks', 'Next.js Routing', 'API Routes'],
                'timeline' => '8-12 weeks',
                'roadmap' => [
                    ['phase' => 'JavaScript Mastery', 'duration' => '3 weeks', 'focus' => 'Learn modern JavaScript and async programming'],
                    ['phase' => 'React Fundamentals', 'duration' => '4 weeks', 'focus' => 'Master components, state, and hooks'],
                    ['phase' => 'Next.js Framework', 'duration' => '4 weeks', 'focus' => 'Build full-stack apps with Next.js']
                ],
                'daily_tasks' => [
                    ['day' => 1, 'title' => 'Learn JavaScript arrow functions', 'description' => 'Practice converting PHP functions to JS arrow functions', 'duration' => 10],
                    ['day' => 2, 'title' => 'Master async/await', 'description' => 'Build a simple API fetcher using fetch() and async/await', 'duration' => 10],
                    ['day' => 3, 'title' => 'Practice array methods', 'description' => 'Use map(), filter(), reduce() instead of PHP foreach', 'duration' => 10],
                    ['day' => 4, 'title' => 'Learn destructuring', 'description' => 'Practice object and array destructuring syntax', 'duration' => 10],
                    ['day' => 5, 'title' => 'Understand promises', 'description' => 'Create and chain promises for sequential operations', 'duration' => 10],
                    ['day' => 6, 'title' => 'Build first React component', 'description' => 'Create a simple UserCard component with props', 'duration' => 10],
                    ['day' => 7, 'title' => 'Practice JSX syntax', 'description' => 'Convert PHP templates to JSX components', 'duration' => 10],
                    ['day' => 8, 'title' => 'Learn useState hook', 'description' => 'Build a counter app using useState', 'duration' => 10],
                    ['day' => 9, 'title' => 'Master useEffect', 'description' => 'Fetch data from API using useEffect hook', 'duration' => 10],
                    ['day' => 10, 'title' => 'Build Todo List', 'description' => 'Create a todo app with add/delete functionality', 'duration' => 10],
                    ['day' => 11, 'title' => 'Learn Next.js routing', 'description' => 'Create 3 pages using Next.js file-based routing', 'duration' => 10],
                    ['day' => 12, 'title' => 'Build dynamic routes', 'description' => 'Create [id].js dynamic route for user profiles', 'duration' => 10],
                    ['day' => 13, 'title' => 'Create API route', 'description' => 'Build /api/users endpoint in Next.js', 'duration' => 10],
                    ['day' => 14, 'title' => 'Practice getServerSideProps', 'description' => 'Fetch data server-side like PHP', 'duration' => 10],
                    ['day' => 15, 'title' => 'Build full CRUD app', 'description' => 'Create a simple blog with Next.js', 'duration' => 10]
                ]
            ];
        }
        
        // Generic fallback
        return [
            'skill_gaps' => ['Core Skills', 'Best Practices', 'Industry Tools', 'Advanced Concepts'],
            'timeline' => '12-16 weeks',
            'roadmap' => [
                ['phase' => 'Foundation', 'duration' => '4 weeks', 'focus' => 'Learn fundamentals'],
                ['phase' => 'Advanced', 'duration' => '8 weeks', 'focus' => 'Master advanced topics']
            ],
            'daily_tasks' => [
                ['day' => 1, 'title' => 'Research target role', 'description' => 'Study job descriptions and requirements', 'duration' => 10],
                ['day' => 2, 'title' => 'Identify skill gaps', 'description' => 'List skills you need to learn', 'duration' => 10],
                ['day' => 3, 'title' => 'Create learning plan', 'description' => 'Organize topics by priority', 'duration' => 10],
                ['day' => 4, 'title' => 'Start first tutorial', 'description' => 'Complete beginner tutorial', 'duration' => 10],
                ['day' => 5, 'title' => 'Practice daily', 'description' => 'Code for 10 minutes', 'duration' => 10]
            ]
        ];
    }

    private function getFallbackCourse($currentRole, $targetRole)
    {
        $lowerCurrent = strtolower($currentRole);
        $lowerTarget = strtolower($targetRole);
        
        if (strpos($lowerCurrent, 'php') !== false && (strpos($lowerTarget, 'next') !== false || strpos($lowerTarget, 'react') !== false)) {
            return [
                'modules' => [
                    [
                        'number' => 1,
                        'title' => 'JavaScript Fundamentals for PHP Developers',
                        'description' => 'Master modern JavaScript coming from PHP',
                        'weeks' => 3,
                        'lessons' => [
                            [
                                'number' => 1,
                                'title' => 'JavaScript vs PHP: Key Differences',
                                'content' => "WEEK 1: Understanding JavaScript Mindset\n\n=== Core Differences ===\n\n1. ASYNCHRONOUS PROGRAMMING\nPHP (Blocking):\n  file_get_contents() waits for response\n  Everything runs line by line\n\nJavaScript (Non-blocking):\n  fetch() returns immediately\n  Use async/await or .then()\n\nReal Example:\n// PHP\ndata = file_get_contents('api/users');\nusers = json_decode(data);\necho users[0]['name'];\n\n// JavaScript\nconst response = await fetch('/api/users');\nconst users = await response.json();\nconsole.log(users[0].name);\n\n2. TYPE SYSTEM\nPHP: Can declare types (string, int, array)\nJS: Everything is var/let/const, types are dynamic\n\n3. ARRAY vs OBJECT\nPHP arrays = JS arrays + objects combined\nPHP: array('key' => 'value')\nJS: {key: 'value'} or ['item1', 'item2']\n\n4. EXECUTION\nPHP: Server-side only (Apache/Nginx)\nJS: Browser + Server (Node.js)\n\n=== Practice Exercise ===\nConvert this PHP to JavaScript:\n\nPHP Code:\nfunction getUsers() {\n  users = ['Alice', 'Bob', 'Charlie'];\n  foreach(users as user) {\n    echo user . ' ';\n  }\n}\n\nJavaScript Solution:\nfunction getUsers() {\n  const users = ['Alice', 'Bob', 'Charlie'];\n  users.forEach(user => {\n    console.log(user);\n  });\n}\n\n=== Key Takeaway ===\nThink ASYNC first in JavaScript!\nPHP waits, JavaScript continues.",
                                'resources' => json_encode([
                                    'https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide',
                                    'https://javascript.info/',
                                    'https://www.youtube.com/watch?v=W6NZfCO5SIk (JavaScript Crash Course)',
                                    'https://eloquentjavascript.net/ (Free Book)'
                                ]),
                                'exercises' => json_encode([
                                    'Convert 5 PHP functions to JavaScript on CodePen',
                                    'Build async data fetcher using fetch() API',
                                    'Create promise chain for sequential API calls',
                                    'Practice on https://www.freecodecamp.org/learn/javascript-algorithms-and-data-structures/'
                                ])
                            ],
                            [
                                'number' => 2,
                                'title' => 'ES6+ Modern JavaScript Features',
                                'content' => "WEEK 2-3: Modern JavaScript You Must Know\n\n=== 1. ARROW FUNCTIONS ===\nOld Way:\nfunction add(a, b) {\n  return a + b;\n}\n\nModern Way:\nconst add = (a, b) => a + b;\n\nWith PHP Comparison:\nPHP: function(a, b) { return a + b; }\nJS:  (a, b) => a + b\n\n=== 2. DESTRUCTURING ===\nExtract values easily:\n\nArray Destructuring:\nconst [first, second] = ['Alice', 'Bob'];\n// first = 'Alice', second = 'Bob'\n\nObject Destructuring:\nconst user = {name: 'Alice', age: 25};\nconst {name, age} = user;\n// name = 'Alice', age = 25\n\nPHP Equivalent:\nlist(first, second) = ['Alice', 'Bob'];\n\n=== 3. SPREAD OPERATOR ===\nCopy and merge arrays/objects:\n\nconst arr1 = [1, 2, 3];\nconst arr2 = [...arr1, 4, 5];\n// [1, 2, 3, 4, 5]\n\nconst user = {name: 'Alice'};\nconst fullUser = {...user, age: 25};\n// {name: 'Alice', age: 25}\n\nPHP: array_merge()\n\n=== 4. TEMPLATE LITERALS ===\nString interpolation (like PHP):\n\nPHP: echo \"Hello name, you are age\";\nJS:  console.log('Hello name, you are age');\n\n=== 5. MODULES (Import/Export) ===\nOrganize code like PHP includes:\n\n// utils.js\nexport const add = (a, b) => a + b;\nexport default function multiply(a, b) {\n  return a * b;\n}\n\n// app.js\nimport multiply, { add } from './utils.js';\n\nPHP Equivalent:\nrequire_once 'utils.php';\n\n=== REAL PROJECT ===\nBuild a User Manager:\n- Fetch users from API\n- Use destructuring to extract data\n- Use arrow functions for callbacks\n- Use spread to add new users\n\nCode Example:\nconst fetchUsers = async () => {\n  const response = await fetch('/api/users');\n  const users = await response.json();\n  return users.map(({id, name, email}) => ({\n    id,\n    displayName: name,\n    contact: email\n  }));\n};\n\nconst addUser = (users, newUser) => [...users, newUser];",
                                'resources' => json_encode([
                                    'https://es6.io/ (Wes Bos ES6 Course)',
                                    'https://github.com/lukehoban/es6features',
                                    'https://www.youtube.com/watch?v=nZ1DMMsyVyI (ES6 Tutorial)',
                                    'https://babeljs.io/docs/en/learn (ES6 Features)'
                                ]),
                                'exercises' => json_encode([
                                    'Refactor 10 old JS functions to ES6 arrow functions',
                                    'Build a shopping cart using spread operator',
                                    'Create module system for calculator app',
                                    'Practice destructuring on https://www.jschallenger.com/'
                                ])
                            ],
                            [
                                'number' => 3,
                                'title' => 'Promises and Async/Await Deep Dive',
                                'content' => "WEEK 3: Master Asynchronous JavaScript\n\n=== WHY ASYNC MATTERS ===\nPHP: Wait for database, then continue\nJS: Start database call, do other things, handle result later\n\n=== PROMISES ===\nA promise is like a receipt for future data\n\nBasic Promise:\nconst promise = new Promise((resolve, reject) => {\n  setTimeout(() => {\n    resolve('Data loaded!');\n  }, 2000);\n});\n\npromise.then(data => console.log(data));\n\n=== ASYNC/AWAIT (Cleaner) ===\nMakes async code look synchronous:\n\nOld Way (Callbacks):\nfetch('/api/users')\n  .then(res => res.json())\n  .then(users => console.log(users))\n  .catch(err => console.error(err));\n\nModern Way (Async/Await):\nasync function getUsers() {\n  try {\n    const res = await fetch('/api/users');\n    const users = await res.json();\n    console.log(users);\n  } catch(err) {\n    console.error(err);\n  }\n}\n\n=== REAL WORLD EXAMPLE ===\nFetch user, then their posts, then comments:\n\nasync function getUserData(userId) {\n  const user = await fetch('/api/users/' + userId).then(r => r.json());\n  const posts = await fetch('/api/posts?user=' + userId).then(r => r.json());\n  const comments = await fetch('/api/comments?user=' + userId).then(r => r.json());\n  \n  return { user, posts, comments };\n}\n\n=== PARALLEL REQUESTS ===\nDon't wait unnecessarily:\n\n// Sequential (slow)\nconst users = await fetch('/api/users');\nconst posts = await fetch('/api/posts');\n\n// Parallel (fast)\nconst [users, posts] = await Promise.all([\n  fetch('/api/users'),\n  fetch('/api/posts')\n]);\n\n=== ERROR HANDLING ===\ntry {\n  const data = await riskyOperation();\n} catch(error) {\n  console.error('Failed:', error.message);\n} finally {\n  console.log('Cleanup');\n}",
                                'resources' => json_encode([
                                    'https://javascript.info/async',
                                    'https://www.youtube.com/watch?v=V_Kr9OSfDeU (Async JS Crash Course)',
                                    'https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise',
                                    'https://www.promisejs.org/'
                                ]),
                                'exercises' => json_encode([
                                    'Build weather app using async/await with OpenWeather API',
                                    'Create promise-based delay function',
                                    'Fetch multiple APIs in parallel using Promise.all()',
                                    'Handle errors gracefully in async functions'
                                ])
                            ]
                        ]
                    ],
                    [
                        'number' => 2,
                        'title' => 'React Fundamentals',
                        'description' => 'Learn React from a backend perspective',
                        'weeks' => 4,
                        'lessons' => [
                            [
                                'number' => 1,
                                'title' => 'React Components and JSX',
                                'content' => "WEEK 4-5: Building UI with React\n\n=== WHAT IS REACT? ===\nReact = JavaScript library for building UIs\nThink of it as: PHP templates + automatic re-rendering\n\n=== COMPONENTS (Like PHP Functions) ===\n\nPHP Template:\nfunction userCard(name, email) {\n  echo '<div class=\"card\">';\n  echo '<h3>' . name . '</h3>';\n  echo '<p>' . email . '</p>';\n  echo '</div>';\n}\n\nReact Component:\nfunction UserCard({name, email}) {\n  return (\n    <div className=\"card\">\n      <h3>{name}</h3>\n      <p>{email}</p>\n    </div>\n  );\n}\n\nUsage:\n<UserCard name=\"Alice\" email=\"alice@example.com\" />\n\n=== JSX = HTML + JavaScript ===\nJSX looks like HTML but it's JavaScript:\n\nconst element = <h1>Hello World</h1>;\nconst dynamic = <h1>Hello {userName}</h1>;\nconst conditional = <div>{isLoggedIn ? 'Welcome' : 'Login'}</div>;\n\n=== PROPS (Like Function Parameters) ===\nPass data to components:\n\nfunction Greeting({name, age}) {\n  return <p>Hello {name}, you are {age} years old</p>;\n}\n\n<Greeting name=\"Alice\" age={25} />\n\n=== LISTS (Like PHP foreach) ===\n\nPHP:\nforeach(users as user) {\n  echo '<li>' . user['name'] . '</li>';\n}\n\nReact:\nusers.map(user => (\n  <li key={user.id}>{user.name}</li>\n))\n\n=== REAL PROJECT ===\nBuild a Product List:\n\nfunction ProductList({products}) {\n  return (\n    <div className=\"products\">\n      {products.map(product => (\n        <div key={product.id} className=\"product-card\">\n          <img src={product.image} alt={product.name} />\n          <h3>{product.name}</h3>\n          <p>Price: {product.price}</p>\n          <button>Add to Cart</button>\n        </div>\n      ))}\n    </div>\n  );\n}\n\n=== KEY DIFFERENCE FROM PHP ===\nPHP: Generate HTML once, send to browser\nReact: Generate HTML, update automatically when data changes!",
                                'resources' => json_encode([
                                    'https://react.dev/learn (Official React Docs)',
                                    'https://www.youtube.com/watch?v=SqcY0GlETPk (React Course)',
                                    'https://scrimba.com/learn/learnreact (Interactive Tutorial)',
                                    'https://react.dev/learn/tutorial-tic-tac-toe (Build Tic-Tac-Toe)'
                                ]),
                                'exercises' => json_encode([
                                    'Build 10 simple components (Button, Card, Header, etc)',
                                    'Create a Todo List with add/delete functionality',
                                    'Build a user profile card with props',
                                    'Practice on https://codesandbox.io/s/new (React Sandbox)'
                                ])
                            ]
                        ]
                    ]
                ]
            ];
        }
        
        return [
            'modules' => [
                [
                    'number' => 1,
                    'title' => 'Career Transition Fundamentals',
                    'description' => 'Core concepts for your transition',
                    'weeks' => 3,
                    'lessons' => [
                        [
                            'number' => 1,
                            'title' => 'Understanding ' . $targetRole,
                            'content' => "Key skills and concepts for {$targetRole}:\n\n1. Core Technologies\n2. Best Practices\n3. Industry Standards\n4. Common Patterns\n\nTransition Strategy:\n- Build on existing {$currentRole} knowledge\n- Focus on transferable skills\n- Practice daily\n- Build portfolio projects",
                            'resources' => json_encode(['Official Documentation', 'Online Courses', 'Community Forums']),
                            'exercises' => json_encode(['Research role requirements', 'Identify skill gaps', 'Create learning plan'])
                        ]
                    ]
                ]
            ]
        ];
    }

    private function callOpenAI($prompt)
    {
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => 'gpt-4',
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.7
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? '{}';
    }
}
