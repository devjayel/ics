import { dashboard, login } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { FileText } from 'lucide-react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Welcome to ICS 211 Form System" />
            <div className="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 p-6 dark:from-slate-950 dark:to-slate-900">
                <div className="w-full max-w-md space-y-8 text-center">
                    {/* Icon/Logo */}
                    <div className="flex justify-center">
                        <div className="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg">
                            <FileText className="h-10 w-10 text-white" />
                        </div>
                    </div>

                    {/* Title and Description */}
                    <div className="space-y-3">
                        <h1 className="text-5xl font-bold text-slate-900 dark:text-slate-100">
                            ICS 211 Form System
                        </h1>
                        <p className="text-lg text-slate-600 dark:text-slate-400">
                            Efficient incident resource management and documentation
                        </p>
                    </div>

                    {/* Description */}
                    <p className="text-base text-slate-500 dark:text-slate-500">
                        A comprehensive system for managing ICS 211 forms, resource unit leaders,
                        personnel tracking, and incident documentation.
                    </p>

                    {/* Login Button - Centered */}
                    <div className="flex flex-col items-center gap-4 pt-4">
                        {auth.user ? (
                            <Link href={dashboard()}>
                                <Button size="lg" className="w-full max-w-xs">
                                    Go to Dashboard
                                </Button>
                            </Link>
                        ) : (
                            <Link href={login()}>
                                <Button size="lg" className="w-full max-w-xs">
                                    Log In to Continue
                                </Button>
                            </Link>
                        )}
                    </div>

                    {/* Footer Info */}
                    <div className="pt-8 text-sm text-slate-400 dark:text-slate-600">
                        Secure • Efficient • Reliable
                    </div>
                </div>
            </div>
        </>
    );
}
    