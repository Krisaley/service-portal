#!/usr/bin/env python3
"""
Ollama Integration Client for Laravel FSM Phase 1
Interfaces with local Ollama instances for code analysis and generation
"""

import requests
import json
import sys
import argparse
from typing import Optional, Dict, List
import time

class OllamaClient:
    """Client for interacting with Ollama instances"""

    # Available Ollama instances
    INSTANCES = {
        'rtx3060': {
            'url': 'http://192.168.0.104:11434',
            'gpu': 'RTX3060',
            'vram': '12GB',
            'description': 'Primary instance with more VRAM'
        },
        # 'vega64': {
        #     'url': 'http://192.168.0.5:11434',
        #     'gpu': 'AMD VEGA64',
        #     'vram': '8GB',
        #     'description': 'Secondary instance'
        # }
    }

    def __init__(self, instance: str = 'rtx3060', model: str = 'deepseek-coder:6.7b'):
        """
        Initialize Ollama client

        Args:
            instance: Which Ollama instance to use ('rtx3060' or 'vega64')
            model: Model name to use (default: deepseek-coder:6.7b)
        """
        if instance not in self.INSTANCES:
            raise ValueError(f"Unknown instance '{instance}'. Use 'rtx3060' or 'vega64'")

        self.instance = instance
        self.base_url = self.INSTANCES[instance]['url']
        self.model = model
        self.gpu_info = self.INSTANCES[instance]

    def generate(self, prompt: str, system: Optional[str] = None,
                 temperature: float = 0.7, stream: bool = False) -> Dict:
        """
        Generate completion from Ollama

        Args:
            prompt: The prompt to send
            system: Optional system prompt
            temperature: Sampling temperature (0.0-1.0)
            stream: Whether to stream the response

        Returns:
            Response dictionary with 'response', 'model', 'created_at', etc.
        """
        url = f"{self.base_url}/api/generate"

        payload = {
            "model": self.model,
            "prompt": prompt,
            "stream": stream,
            "options": {
                "temperature": temperature
            }
        }

        if system:
            payload["system"] = system

        try:
            response = requests.post(url, json=payload, timeout=300)
            response.raise_for_status()

            if stream:
                return self._handle_stream(response)
            else:
                return response.json()

        except requests.exceptions.RequestException as e:
            return {
                "error": str(e),
                "instance": self.instance,
                "url": self.base_url
            }

    def _handle_stream(self, response):
        """Handle streaming response"""
        full_response = ""

        for line in response.iter_lines():
            if line:
                data = json.loads(line)
                if 'response' in data:
                    chunk = data['response']
                    full_response += chunk
                    print(chunk, end='', flush=True)

                if data.get('done', False):
                    print()  # New line at end
                    return {
                        "response": full_response,
                        "model": data.get('model'),
                        "created_at": data.get('created_at'),
                        "done": True
                    }

        return {"response": full_response}

    def chat(self, messages: List[Dict], temperature: float = 0.7) -> Dict:
        """
        Chat with Ollama using message history

        Args:
            messages: List of message dicts with 'role' and 'content'
            temperature: Sampling temperature

        Returns:
            Response dictionary
        """
        url = f"{self.base_url}/api/chat"

        payload = {
            "model": self.model,
            "messages": messages,
            "stream": False,
            "options": {
                "temperature": temperature
            }
        }

        try:
            response = requests.post(url, json=payload, timeout=300)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            return {"error": str(e)}

    def check_health(self) -> bool:
        """Check if Ollama instance is healthy"""
        try:
            response = requests.get(f"{self.base_url}/api/tags", timeout=5)
            return response.status_code == 200
        except:
            return False

    def list_models(self) -> List[str]:
        """List available models on this instance"""
        try:
            response = requests.get(f"{self.base_url}/api/tags", timeout=5)
            response.raise_for_status()
            data = response.json()
            return [model['name'] for model in data.get('models', [])]
        except:
            return []


class CodeAnalyzer:
    """High-level code analysis using Ollama"""

    def __init__(self, client: OllamaClient):
        self.client = client

    def review_code(self, code: str, language: str = 'php') -> str:
        """
        Review code for issues, best practices, security

        Args:
            code: The code to review
            language: Programming language

        Returns:
            Review feedback
        """
        system_prompt = f"""You are an expert {language.upper()} code reviewer.
Analyze the code for:
1. Security vulnerabilities (SQL injection, XSS, etc.)
2. Performance issues
3. Code quality and best practices
4. Potential bugs
5. Suggestions for improvement

Be concise but thorough. Use markdown formatting."""

        prompt = f"""Review this {language.upper()} code:

```{language}
{code}
```

Provide your analysis:"""

        response = self.client.generate(prompt, system=system_prompt, temperature=0.3)
        return response.get('response', response.get('error', 'No response'))

    def explain_code(self, code: str, language: str = 'php') -> str:
        """Explain what code does"""
        system_prompt = f"You are an expert {language.upper()} developer. Explain code clearly and concisely."

        prompt = f"""Explain what this {language.upper()} code does:

```{language}
{code}
```

Provide a clear explanation:"""

        response = self.client.generate(prompt, system=system_prompt, temperature=0.5)
        return response.get('response', response.get('error', 'No response'))

    def suggest_improvements(self, code: str, language: str = 'php') -> str:
        """Suggest code improvements"""
        system_prompt = f"You are an expert {language.upper()} developer focused on clean code and best practices."

        prompt = f"""Suggest improvements for this {language.upper()} code:

```{language}
{code}
```

Provide specific, actionable improvements:"""

        response = self.client.generate(prompt, system=system_prompt, temperature=0.6)
        return response.get('response', response.get('error', 'No response'))

    def generate_tests(self, code: str, language: str = 'php', framework: str = 'pest') -> str:
        """Generate test cases for code"""
        system_prompt = f"You are an expert at writing {framework} tests for {language.upper()} code."

        prompt = f"""Generate {framework} test cases for this {language.upper()} code:

```{language}
{code}
```

Generate comprehensive tests:"""

        response = self.client.generate(prompt, system=system_prompt, temperature=0.4)
        return response.get('response', response.get('error', 'No response'))

    def check_security(self, code: str, language: str = 'php') -> str:
        """Security-focused code analysis"""
        system_prompt = f"""You are a security expert specializing in {language.upper()}.
Focus on finding security vulnerabilities:
- SQL injection
- XSS (Cross-Site Scripting)
- CSRF (Cross-Site Request Forgery)
- Authentication/Authorization issues
- Data exposure
- Input validation issues"""

        prompt = f"""Perform a security audit on this {language.upper()} code:

```{language}
{code}
```

List all security concerns:"""

        response = self.client.generate(prompt, system=system_prompt, temperature=0.2)
        return response.get('response', response.get('error', 'No response'))


def main():
    """CLI interface"""
    parser = argparse.ArgumentParser(
        description='Ollama Integration for Laravel FSM Phase 1',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  # Check health of both instances
  python ollama-client.py --health

  # Review a PHP file
  python ollama-client.py --review app/Models/User.php

  # Generate tests for a file
  python ollama-client.py --generate-tests app/Services/ModuleService.php

  # Security audit
  python ollama-client.py --security app/Http/Controllers/ApiController.php

  # Use specific instance
  python ollama-client.py --instance vega64 --review some-file.php

  # Interactive prompt
  python ollama-client.py --prompt "Explain Laravel Jetstream multi-tenancy"
        """
    )

    parser.add_argument('--instance', choices=['rtx3060', 'vega64'], default='rtx3060',
                        help='Which Ollama instance to use')
    parser.add_argument('--model', default='deepseek-coder:6.7b',
                        help='Model to use')
    parser.add_argument('--health', action='store_true',
                        help='Check health of Ollama instances')
    parser.add_argument('--list-models', action='store_true',
                        help='List available models')
    parser.add_argument('--review', metavar='FILE',
                        help='Review code in file')
    parser.add_argument('--explain', metavar='FILE',
                        help='Explain code in file')
    parser.add_argument('--improve', metavar='FILE',
                        help='Suggest improvements for file')
    parser.add_argument('--generate-tests', metavar='FILE',
                        help='Generate tests for file')
    parser.add_argument('--security', metavar='FILE',
                        help='Security audit of file')
    parser.add_argument('--prompt', metavar='TEXT',
                        help='Send custom prompt')
    parser.add_argument('--language', default='php',
                        help='Programming language (default: php)')

    args = parser.parse_args()

    # Health check
    if args.health:
        print("Checking Ollama instances...\n")
        for name, info in OllamaClient.INSTANCES.items():
            client = OllamaClient(name)
            healthy = client.check_health()
            status = "[ONLINE]" if healthy else "[OFFLINE]"
            print(f"{status} - {name.upper()}: {info['url']} ({info['gpu']}, {info['vram']})")
            if healthy:
                models = client.list_models()
                print(f"  Models: {', '.join(models) if models else 'None'}")
        print()
        return

    # List models
    if args.list_models:
        client = OllamaClient(args.instance, args.model)
        models = client.list_models()
        print(f"\nModels on {args.instance}:")
        for model in models:
            print(f"  - {model}")
        print()
        return

    # Initialize client and analyzer
    client = OllamaClient(args.instance, args.model)
    analyzer = CodeAnalyzer(client)

    print(f"Using: {args.instance.upper()} ({client.gpu_info['gpu']}) - {args.model}\n")

    # Read file if needed
    code = None
    if any([args.review, args.explain, args.improve, args.generate_tests, args.security]):
        file_path = args.review or args.explain or args.improve or args.generate_tests or args.security
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                code = f.read()
        except FileNotFoundError:
            print(f"Error: File not found: {file_path}")
            sys.exit(1)
        except Exception as e:
            print(f"Error reading file: {e}")
            sys.exit(1)

    # Execute action
    if args.review:
        print("[CODE REVIEW]\n")
        result = analyzer.review_code(code, args.language)
        print(result)

    elif args.explain:
        print("[CODE EXPLANATION]\n")
        result = analyzer.explain_code(code, args.language)
        print(result)

    elif args.improve:
        print("[IMPROVEMENT SUGGESTIONS]\n")
        result = analyzer.suggest_improvements(code, args.language)
        print(result)

    elif args.generate_tests:
        print("[GENERATED TESTS]\n")
        result = analyzer.generate_tests(code, args.language)
        print(result)

    elif args.security:
        print("[SECURITY AUDIT]\n")
        result = analyzer.check_security(code, args.language)
        print(result)

    elif args.prompt:
        print("[RESPONSE]\n")
        response = client.generate(args.prompt, temperature=0.7)
        print(response.get('response', response.get('error', 'No response')))

    else:
        parser.print_help()


if __name__ == '__main__':
    main()
