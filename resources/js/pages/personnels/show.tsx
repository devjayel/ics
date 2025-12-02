import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/personnels';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { ArrowLeft, Edit } from 'lucide-react';

interface Personnel {
    uuid: string;
    name: string;
    contact_number: string;
    serial_number: string;
    department: string;
    created_at: string;
}

interface ShowProps {
    personnel: Personnel;
}

const breadcrumbs = (personnel: Personnel): BreadcrumbItem[] => [
    {
        title: 'Personnels',
        href: index().url,
    },
    {
        title: personnel.name,
        href: '#',
    },
];

export default function Show({ personnel }: ShowProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs(personnel)}>
            <Head title={`Personnel - ${personnel.name}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-4">
                                <Link href={index().url}>
                                    <Button variant="outline" size="icon">
                                        <ArrowLeft className="h-4 w-4" />
                                    </Button>
                                </Link>
                                <div>
                                    <CardTitle>Personnel Details</CardTitle>
                                    <CardDescription>
                                        View personnel information
                                    </CardDescription>
                                </div>
                            </div>
                            <Link href={`/personnels/${personnel.uuid}/edit`}>
                                <Button>
                                    <Edit className="mr-2 h-4 w-4" />
                                    Edit
                                </Button>
                            </Link>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-6">
                            <div className="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label className="text-sm font-medium text-gray-500">
                                        Name
                                    </label>
                                    <p className="mt-1 text-base font-medium">
                                        {personnel.name}
                                    </p>
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-500">
                                        Serial Number
                                    </label>
                                    <p className="mt-1 text-base">
                                        {personnel.serial_number}
                                    </p>
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-500">
                                        Contact Number
                                    </label>
                                    <p className="mt-1 text-base">
                                        {personnel.contact_number}
                                    </p>
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-500">
                                        Department
                                    </label>
                                    <p className="mt-1 text-base">
                                        {personnel.department}
                                    </p>
                                </div>

                                <div>
                                    <label className="text-sm font-medium text-gray-500">
                                        Created At
                                    </label>
                                    <p className="mt-1 text-base">
                                        {new Date(personnel.created_at).toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        })}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
