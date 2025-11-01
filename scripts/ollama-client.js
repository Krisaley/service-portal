#!/usr/bin/env node
/**
 * Ollama Integration Client for Laravel FSM Phase 1 (Node.js)
 * Interfaces with local Ollama instances for code analysis and generation
 */

const axios = require('axios');
const fs = require('fs').promises;
const path = require('path');

// Available Ollama instances
const INSTANCES = {
    rtx3060: {
        url: 'http://192.168.0.104:11434',
        gpu: 'RTX3060',
        vram: '12GB',
        description: 'Primary instance with more VRAM'
    },
    // vega64: {
    //     url: 'http://192.168.0.5:11434',
    //     gpu: 'AMD VEGA64',
    //     vram: '8GB',
    //     description: 'Secondary instance'
    // }
};

class OllamaClient {
    constructor(instance = 'rtx3060', model = 'deepseek-coder:6.7b') {
        if (!INSTANCES[instance]) {
            throw new Error(`Unknown instance '${instance}'. Use 'rtx3060' or 'vega64'`);
        }

        this.instance = instance;
        this.baseUrl = INSTANCES[instance].url;
        this.model = model;
        this.gpuInfo = INSTANCES[instance];
    }

    async generate(prompt, options = {}) {
        const {
            system = null,
            temperature = 0.7,
            stream = false
        } = options;

        const url = `${this.baseUrl}/api/generate`;

        const payload = {
            model: this.model,
            prompt,
            stream,
            options: {
                temperature
            }
        };

        if (system) {
            payload.system = system;
        }

        try {
            const response = await axios.post(url, payload, {
                timeout: 300000,
                responseType: stream ? 'stream' : 'json'
            });

            if (stream) {
                return this._handleStream(response);
            } else {
                return response.data;
            }
        } catch (error) {
            return {
                error: error.message,
                instance: this.instance,
                url: this.baseUrl
            };
        }
    }

    async _handleStream(response) {
        return new Promise((resolve, reject) => {
            let fullResponse = '';

            response.data.on('data', (chunk) => {
                const lines = chunk.toString().split('\n').filter(line => line.trim());

                for (const line of lines) {
                    try {
                        const data = JSON.parse(line);
                        if (data.response) {
                            fullResponse += data.response;
                            process.stdout.write(data.response);
                        }

                        if (data.done) {
                            console.log(); // New line
                            resolve({
                                response: fullResponse,
                                model: data.model,
                                created_at: data.created_at,
                                done: true
                            });
                        }
                    } catch (e) {
                        // Skip invalid JSON lines
                    }
                }
            });

            response.data.on('end', () => {
                resolve({ response: fullResponse });
            });

            response.data.on('error', reject);
        });
    }

    async chat(messages, temperature = 0.7) {
        const url = `${this.baseUrl}/api/chat`;

        const payload = {
            model: this.model,
            messages,
            stream: false,
            options: {
                temperature
            }
        };

        try {
            const response = await axios.post(url, payload, { timeout: 300000 });
            return response.data;
        } catch (error) {
            return { error: error.message };
        }
    }

    async checkHealth() {
        try {
            const response = await axios.get(`${this.baseUrl}/api/tags`, { timeout: 5000 });
            return response.status === 200;
        } catch {
            return false;
        }
    }

    async listModels() {
        try {
            const response = await axios.get(`${this.baseUrl}/api/tags`, { timeout: 5000 });
            return response.data.models?.map(m => m.name) || [];
        } catch {
            return [];
        }
    }
}

class CodeAnalyzer {
    constructor(client) {
        this.client = client;
    }

    async reviewCode(code, language = 'php') {
        const systemPrompt = `You are an expert ${language.toUpperCase()} code reviewer.
Analyze the code for:
1. Security vulnerabilities (SQL injection, XSS, etc.)
2. Performance issues
3. Code quality and best practices
4. Potential bugs
5. Suggestions for improvement

Be concise but thorough. Use markdown formatting.`;

        const prompt = `Review this ${language.toUpperCase()} code:

\`\`\`${language}
${code}
\`\`\`

Provide your analysis:`;

        const response = await this.client.generate(prompt, {
            system: systemPrompt,
            temperature: 0.3
        });

        return response.response || response.error || 'No response';
    }

    async explainCode(code, language = 'php') {
        const systemPrompt = `You are an expert ${language.toUpperCase()} developer. Explain code clearly and concisely.`;

        const prompt = `Explain what this ${language.toUpperCase()} code does:

\`\`\`${language}
${code}
\`\`\`

Provide a clear explanation:`;

        const response = await this.client.generate(prompt, {
            system: systemPrompt,
            temperature: 0.5
        });

        return response.response || response.error || 'No response';
    }

    async suggestImprovements(code, language = 'php') {
        const systemPrompt = `You are an expert ${language.toUpperCase()} developer focused on clean code and best practices.`;

        const prompt = `Suggest improvements for this ${language.toUpperCase()} code:

\`\`\`${language}
${code}
\`\`\`

Provide specific, actionable improvements:`;

        const response = await this.client.generate(prompt, {
            system: systemPrompt,
            temperature: 0.6
        });

        return response.response || response.error || 'No response';
    }

    async generateTests(code, language = 'php', framework = 'pest') {
        const systemPrompt = `You are an expert at writing ${framework} tests for ${language.toUpperCase()} code.`;

        const prompt = `Generate ${framework} test cases for this ${language.toUpperCase()} code:

\`\`\`${language}
${code}
\`\`\`

Generate comprehensive tests:`;

        const response = await this.client.generate(prompt, {
            system: systemPrompt,
            temperature: 0.4
        });

        return response.response || response.error || 'No response';
    }

    async checkSecurity(code, language = 'php') {
        const systemPrompt = `You are a security expert specializing in ${language.toUpperCase()}.
Focus on finding security vulnerabilities:
- SQL injection
- XSS (Cross-Site Scripting)
- CSRF (Cross-Site Request Forgery)
- Authentication/Authorization issues
- Data exposure
- Input validation issues`;

        const prompt = `Perform a security audit on this ${language.toUpperCase()} code:

\`\`\`${language}
${code}
\`\`\`

List all security concerns:`;

        const response = await this.client.generate(prompt, {
            system: systemPrompt,
            temperature: 0.2
        });

        return response.response || response.error || 'No response';
    }
}

// CLI
async function main() {
    const args = process.argv.slice(2);

    if (args.includes('--help') || args.includes('-h')) {
        console.log(`
Ollama Integration for Laravel FSM Phase 1

Usage:
  node ollama-client.js [options]

Options:
  --instance <name>      Which instance to use (rtx3060|vega64) [default: rtx3060]
  --model <name>         Model to use [default: deepseek-coder:6.7b]
  --health               Check health of Ollama instances
  --list-models          List available models
  --review <file>        Review code in file
  --explain <file>       Explain code in file
  --improve <file>       Suggest improvements for file
  --generate-tests <file> Generate tests for file
  --security <file>      Security audit of file
  --prompt <text>        Send custom prompt
  --language <lang>      Programming language [default: php]

Examples:
  node ollama-client.js --health
  node ollama-client.js --review app/Models/User.php
  node ollama-client.js --instance vega64 --security app/Http/Controllers/ApiController.php
  node ollama-client.js --prompt "Explain Laravel Jetstream multi-tenancy"
        `);
        return;
    }

    const getArg = (flag) => {
        const index = args.indexOf(flag);
        return index !== -1 && args[index + 1] ? args[index + 1] : null;
    };

    const instance = getArg('--instance') || 'rtx3060';
    const model = getArg('--model') || 'deepseek-coder:6.7b';
    const language = getArg('--language') || 'php';

    // Health check
    if (args.includes('--health')) {
        console.log('Checking Ollama instances...\n');
        for (const [name, info] of Object.entries(INSTANCES)) {
            const client = new OllamaClient(name);
            const healthy = await client.checkHealth();
            const status = healthy ? '✅ ONLINE' : '❌ OFFLINE';
            console.log(`${status} - ${name.toUpperCase()}: ${info.url} (${info.gpu}, ${info.vram})`);
            if (healthy) {
                const models = await client.listModels();
                console.log(`  Models: ${models.length ? models.join(', ') : 'None'}`);
            }
        }
        console.log();
        return;
    }

    // List models
    if (args.includes('--list-models')) {
        const client = new OllamaClient(instance, model);
        const models = await client.listModels();
        console.log(`\nModels on ${instance}:`);
        models.forEach(m => console.log(`  - ${m}`));
        console.log();
        return;
    }

    // Initialize
    const client = new OllamaClient(instance, model);
    const analyzer = new CodeAnalyzer(client);

    console.log(`Using: ${instance.toUpperCase()} (${client.gpuInfo.gpu}) - ${model}\n`);

    // Read file if needed
    let code = null;
    const fileArg = getArg('--review') || getArg('--explain') || getArg('--improve') ||
                    getArg('--generate-tests') || getArg('--security');

    if (fileArg) {
        try {
            code = await fs.readFile(fileArg, 'utf-8');
        } catch (error) {
            console.error(`Error reading file: ${error.message}`);
            process.exit(1);
        }
    }

    // Execute action
    if (args.includes('--review')) {
        console.log('🔍 Code Review:\n');
        const result = await analyzer.reviewCode(code, language);
        console.log(result);
    } else if (args.includes('--explain')) {
        console.log('📖 Code Explanation:\n');
        const result = await analyzer.explainCode(code, language);
        console.log(result);
    } else if (args.includes('--improve')) {
        console.log('💡 Improvement Suggestions:\n');
        const result = await analyzer.suggestImprovements(code, language);
        console.log(result);
    } else if (args.includes('--generate-tests')) {
        console.log('🧪 Generated Tests:\n');
        const result = await analyzer.generateTests(code, language);
        console.log(result);
    } else if (args.includes('--security')) {
        console.log('🔒 Security Audit:\n');
        const result = await analyzer.checkSecurity(code, language);
        console.log(result);
    } else if (args.includes('--prompt')) {
        const promptText = getArg('--prompt');
        console.log('💬 Response:\n');
        const response = await client.generate(promptText, { temperature: 0.7 });
        console.log(response.response || response.error || 'No response');
    } else {
        console.log('Use --help for usage information');
    }
}

// Run if called directly
if (require.main === module) {
    main().catch(console.error);
}

module.exports = { OllamaClient, CodeAnalyzer, INSTANCES };
