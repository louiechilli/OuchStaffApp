import { Head, Link } from '@inertiajs/react';
import { App, Page, Navbar, Block, Button, List, ListItem, Card, Chip } from 'konsta/react';

export default function Dashboard({ auth }) {
    const stats = [
        { label: 'Projects', value: '12', color: 'bg-blue-500' },
        { label: 'Tasks', value: '28', color: 'bg-green-500' },
        { label: 'Messages', value: '5', color: 'bg-purple-500' },
    ];

    const recentActivity = [
        { title: 'Project Alpha Updated', time: '2 hours ago', icon: '📊' },
        { title: 'New message received', time: '4 hours ago', icon: '💬' },
        { title: 'Task completed', time: '1 day ago', icon: '✅' },
        { title: 'File uploaded', time: '2 days ago', icon: '📁' },
    ];

    const quickActions = [
        { title: 'New Project', icon: '➕', color: 'bg-blue-100' },
        { title: 'View Tasks', icon: '📋', color: 'bg-green-100' },
        { title: 'Messages', icon: '💬', color: 'bg-purple-100' },
        { title: 'Settings', icon: '⚙️', color: 'bg-gray-100' },
    ];

    return (
        <>
            <Head title="Dashboard" />
            <App theme="ios">
                <Page>
                    <Navbar 
                        title="Dashboard"
                        right={
                            <Link 
                                href="/profile" 
                                className="text-blue-500 font-semibold"
                            >
                                Profile
                            </Link>
                        }
                    />

                    {/* Welcome Section */}
                    <Block strong inset>
                        <Card>
                            <div className="p-5">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <h2 className="text-2xl font-bold mb-1">
                                            Welcome back, {auth.user.name}!
                                        </h2>
                                        <p className="text-gray-600">
                                            {auth.user.email}
                                        </p>
                                    </div>
                                    <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                        {auth.user.name.charAt(0).toUpperCase()}
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </Block>

                    {/* Stats Cards */}
                    <Block strong inset>
                        <div className="grid grid-cols-3 gap-2">
                            {stats.map((stat, index) => (
                                <Card key={index}>
                                    <div className="p-4 text-center">
                                        <div className={`w-12 h-12 ${stat.color} rounded-full mx-auto mb-2 flex items-center justify-center text-white text-xl font-bold`}>
                                            {stat.value}
                                        </div>
                                        <p className="text-xs text-gray-600">{stat.label}</p>
                                    </div>
                                </Card>
                            ))}
                        </div>
                    </Block>

                    {/* Quick Actions */}
                    <Block>
                        <h3 className="text-lg font-semibold mb-3 px-4">Quick Actions</h3>
                    </Block>
                    <Block strong inset>
                        <div className="grid grid-cols-2 gap-2">
                            {quickActions.map((action, index) => (
                                <Button 
                                    key={index}
                                    large
                                    outline
                                    className="flex-col h-24"
                                >
                                    <span className="text-3xl mb-1">{action.icon}</span>
                                    <span className="text-sm">{action.title}</span>
                                </Button>
                            ))}
                        </div>
                    </Block>

                    {/* Recent Activity */}
                    <Block>
                        <h3 className="text-lg font-semibold mb-3 px-4">Recent Activity</h3>
                    </Block>
                    <List strong inset>
                        {recentActivity.map((activity, index) => (
                            <ListItem
                                key={index}
                                link
                                title={activity.title}
                                after={activity.time}
                            >
                                <div slot="media" className="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-xl">
                                    {activity.icon}
                                </div>
                            </ListItem>
                        ))}
                    </List>

                    {/* System Status */}
                    <Block strong inset>
                        <Card>
                            <div className="p-4">
                                <div className="flex items-center justify-between mb-3">
                                    <h3 className="font-semibold">System Status</h3>
                                    <Chip className="bg-green-100 text-green-800">
                                        All Systems Operational
                                    </Chip>
                                </div>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">API Status</span>
                                        <span className="text-green-600 font-semibold">✓ Online</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Database</span>
                                        <span className="text-green-600 font-semibold">✓ Connected</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Cache</span>
                                        <span className="text-green-600 font-semibold">✓ Active</span>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </Block>

                    {/* Logout Button */}
                    <Block strong inset className="pb-8">
                        <Link href="/logout" method="post" as="button" className="w-full">
                            <Button 
                                large 
                                clear
                                className="w-full text-red-500"
                            >
                                Logout
                            </Button>
                        </Link>
                    </Block>
                </Page>
            </App>
        </>
    );
}