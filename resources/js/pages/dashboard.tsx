import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Users, Shield, Award, TrendingUp } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface Personnel {
    uuid: string;
    name: string;
    department: string;
    created_at: string;
}

interface Rul {
    uuid: string;
    name: string;
    department: string;
    created_at: string;
}

interface Certificate {
    uuid: string;
    certificate_name: string;
    rul: {
        name: string;
    };
    created_at: string;
}

interface DashboardProps {
    stats: {
        personnels: number;
        ruls: number;
        certificates: number;
    };
    recent: {
        personnels: Personnel[];
        ruls: Rul[];
        certificates: Certificate[];
    };
}

export default function Dashboard({ stats, recent }: DashboardProps) {
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                {/* Stats Cards */}
                <div className="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Personnels
                            </CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.personnels}</div>
                            <p className="text-xs text-muted-foreground">
                                Active personnel in the system
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Resource Unit Leaders
                            </CardTitle>
                            <Shield className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.ruls}</div>
                            <p className="text-xs text-muted-foreground">
                                Registered RULs
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Certificates
                            </CardTitle>
                            <Award className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.certificates}</div>
                            <p className="text-xs text-muted-foreground">
                                Certificates uploaded
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Activities */}
                <div className="grid gap-4 md:grid-cols-2">
                    {/* Recent Personnels */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Personnels</CardTitle>
                            <CardDescription>
                                Latest personnel added to the system
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {recent.personnels.length === 0 ? (
                                <p className="text-sm text-muted-foreground text-center py-4">
                                    No personnel records yet
                                </p>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Name</TableHead>
                                            <TableHead>Department</TableHead>
                                            <TableHead>Date</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {recent.personnels.map((personnel) => (
                                            <TableRow key={personnel.uuid}>
                                                <TableCell className="font-medium">
                                                    <Link
                                                        href={`/personnels/${personnel.uuid}`}
                                                        className="hover:underline"
                                                    >
                                                        {personnel.name}
                                                    </Link>
                                                </TableCell>
                                                <TableCell>{personnel.department}</TableCell>
                                                <TableCell className="text-sm text-muted-foreground">
                                                    {formatDate(personnel.created_at)}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            )}
                        </CardContent>
                    </Card>

                    {/* Recent RULs */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent RULs</CardTitle>
                            <CardDescription>
                                Latest Resource Unit Leaders
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {recent.ruls.length === 0 ? (
                                <p className="text-sm text-muted-foreground text-center py-4">
                                    No RUL records yet
                                </p>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Name</TableHead>
                                            <TableHead>Department</TableHead>
                                            <TableHead>Date</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {recent.ruls.map((rul) => (
                                            <TableRow key={rul.uuid}>
                                                <TableCell className="font-medium">
                                                    <Link
                                                        href={`/ruls/${rul.uuid}`}
                                                        className="hover:underline"
                                                    >
                                                        {rul.name}
                                                    </Link>
                                                </TableCell>
                                                <TableCell>{rul.department}</TableCell>
                                                <TableCell className="text-sm text-muted-foreground">
                                                    {formatDate(rul.created_at)}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Certificates */}
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Certificates</CardTitle>
                        <CardDescription>
                            Latest certificates uploaded
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {recent.certificates.length === 0 ? (
                            <p className="text-sm text-muted-foreground text-center py-4">
                                No certificate records yet
                            </p>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Certificate Name</TableHead>
                                        <TableHead>RUL Name</TableHead>
                                        <TableHead>Date</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {recent.certificates.map((certificate) => (
                                        <TableRow key={certificate.uuid}>
                                            <TableCell className="font-medium">
                                                <Link
                                                    href={`/certificates/${certificate.uuid}`}
                                                    className="hover:underline"
                                                >
                                                    {certificate.certificate_name}
                                                </Link>
                                            </TableCell>
                                            <TableCell>{certificate.rul.name}</TableCell>
                                            <TableCell className="text-sm text-muted-foreground">
                                                {formatDate(certificate.created_at)}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
