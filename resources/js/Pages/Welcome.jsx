import { Head } from '@inertiajs/react';
import { App, Page, Navbar, Block, Button, List, ListItem, Card, Link } from 'konsta/react';

export default function Welcome() {
    return (
        <>
            <Head title="Welcome" />
            <App theme="ios">
                <Page>
                    <Navbar 
                        title="Laravel Starter"
                        subtitle="Powered by Konsta UI"
                    />
                    
                    {/* Hero Section */}
                    <Block strong inset className="text-center">
                        <div className="py-8">
                            <div className="mb-4">
                                <svg className="w-20 h-20 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h1 className="text-3xl font-bold mb-3">Welcome to Your App</h1>
                            <p className="text-gray-600 text-lg">
                                A modern mobile-first application built with Laravel and Konsta UI
                            </p>
                        </div>
                    </Block>

                    {/* Features */}
                    <Block strong inset>
                        <Card>
                            <div className="p-4">
                                <h2 className="text-xl font-semibold mb-4">Features</h2>
                                <div className="space-y-3">
                                    <div className="flex items-start gap-3">
                                        <span className="text-2xl">⚡</span>
                                        <div>
                                            <h3 className="font-semibold">Fast & Modern</h3>
                                            <p className="text-sm text-gray-600">Built with latest Laravel and React</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3">
                                        <span className="text-2xl">📱</span>
                                        <div>
                                            <h3 className="font-semibold">Mobile First</h3>
                                            <p className="text-sm text-gray-600">Beautiful UI that works everywhere</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3">
                                        <span className="text-2xl">🔒</span>
                                        <div>
                                            <h3 className="font-semibold">Secure</h3>
                                            <p className="text-sm text-gray-600">Authentication built-in</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </Block>

                    {/* Quick Actions */}
                    <Block strong inset>
                        <div className="space-y-2">
                            <Button 
                                large 
                                href="/dashboard"
                                className="w-full"
                            >
                                Get Started
                            </Button>
                            <Button 
                                large 
                                outline 
                                href="/login"
                                className="w-full"
                            >
                                Login
                            </Button>
                            <Button 
                                large 
                                clear 
                                href="/register"
                                className="w-full"
                            >
                                Create Account
                            </Button>
                        </div>
                    </Block>

                    {/* Navigation List */}
                    <List strong inset>
                        <ListItem
                            link
                            title="Documentation"
                            after="→"
                            subtitle="Learn more about the framework"
                        >
                            <div slot="media" className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                📚
                            </div>
                        </ListItem>
                        <ListItem
                            link
                            title="Components"
                            after="→"
                            subtitle="Explore Konsta UI components"
                        >
                            <div slot="media" className="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                🎨
                            </div>
                        </ListItem>
                        <ListItem
                            link
                            title="API Reference"
                            after="→"
                            subtitle="View API documentation"
                        >
                            <div slot="media" className="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                🔧
                            </div>
                        </ListItem>
                    </List>

                    {/* Footer */}
                    <Block className="text-center text-sm text-gray-500 py-8">
                        <p>Built with ❤️ using Laravel & Konsta UI</p>
                        <p className="mt-2">
                            <Link href="https://laravel.com" target="_blank">Laravel</Link>
                            {' • '}
                            <Link href="https://konstaui.com" target="_blank">Konsta UI</Link>
                        </p>
                    </Block>
                </Page>
            </App>
        </>
    );
}